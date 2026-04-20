<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ChargingSession;
use App\Models\ChargingStation;
use App\Models\Vehicle;
use Illuminate\Support\Collection;

class OperationsSimulationService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function scenarios(): Collection
    {
        $fleetTotal = max(1, Vehicle::query()->count());
        $availableVehicles = Vehicle::query()->where('status', 'available')->count();
        $activeTrips = Booking::query()->whereIn('status', ['confirmed', 'active'])->count();
        $rangeRiskTrips = Booking::query()->where('projected_return_soc', '<', 30)->count();
        $chargingDemand = ChargingSession::query()->whereIn('status', ['scheduled', 'in_progress'])->count();
        $livePorts = max(1, (int) ChargingStation::query()->sum('live_availability'));
        $staleVehicles = Vehicle::query()->where('connectivity_status', 'offline')->count()
            + Vehicle::query()->where('sync_delay_seconds', '>', 30)->count();
        $lowChargeVehicles = Vehicle::query()->where('battery_soc', '<', 30)->count();

        $base = [
            'fleet_total' => $fleetTotal,
            'available_vehicles' => $availableVehicles,
            'active_trips' => $activeTrips,
            'range_risk_trips' => $rangeRiskTrips,
            'charging_demand' => $chargingDemand,
            'live_ports' => $livePorts,
            'stale_vehicles' => $staleVehicles,
            'low_charge_vehicles' => $lowChargeVehicles,
        ];

        $scenarios = collect([
            [
                'title' => 'Peak-hour demand',
                'description' => 'Rush-hour demand surges while chargers operate normally.',
                'demand_factor' => 1.55,
                'charger_factor' => 1.0,
                'range_factor' => 1.0,
            ],
            [
                'title' => 'Charger outage',
                'description' => 'One major charging hub degrades and queue pressure rises.',
                'demand_factor' => 1.15,
                'charger_factor' => 0.58,
                'range_factor' => 1.0,
            ],
            [
                'title' => 'Rain and traffic',
                'description' => 'Traffic plus weather reduce range confidence and increase energy draw.',
                'demand_factor' => 1.30,
                'charger_factor' => 0.85,
                'range_factor' => 0.82,
            ],
        ]);

        return $scenarios->map(function (array $scenario) use ($base): array {
            return [
                ...$scenario,
                'runs' => collect([10, 100])->map(
                    fn (int $multiplier): array => $this->simulateAtScale($base, $scenario, $multiplier)
                ),
            ];
        });
    }

    /**
     * @param  array<string, int>  $base
     * @param  array<string, mixed>  $scenario
     * @return array<string, int|string>
     */
    private function simulateAtScale(array $base, array $scenario, int $multiplier): array
    {
        $fleetTotal = max(1, $base['fleet_total'] * $multiplier);
        $dispatchableVehicles = max(1, (int) round($base['available_vehicles'] * $multiplier * $scenario['range_factor']));
        $projectedTrips = (int) round(max(1, $base['active_trips']) * $multiplier * $scenario['demand_factor']);
        $chargerCapacity = max(1, (int) round($base['live_ports'] * $multiplier * $scenario['charger_factor']));
        $utilizationRate = min(99, (int) round(($projectedTrips / $fleetTotal) * 100));

        $downtimeRate = min(95, (int) round(
            ((($base['charging_demand'] + $base['low_charge_vehicles']) * $multiplier) / $fleetTotal) * 100
            + ((1 - $scenario['charger_factor']) * 22)
            + ((1 - $scenario['range_factor']) * 18)
        ));

        $failedBookingRate = min(88, (int) round(
            max(0, $projectedTrips - $dispatchableVehicles) / max(1, $projectedTrips) * 100
            + ((1 - $scenario['charger_factor']) * 12)
        ));

        $chargerWaitMinutes = (int) round(
            (($base['charging_demand'] * $multiplier) / $chargerCapacity) * 18
            + ((1 - $scenario['charger_factor']) * 40)
        );

        $rangeRiskIncidents = (int) round(
            ($base['range_risk_trips'] + $base['stale_vehicles']) * $multiplier
            * (1 + (1 - $scenario['range_factor']) + (($scenario['demand_factor'] - 1) * 0.4))
        );

        return [
            'label' => "{$multiplier}x fleet",
            'utilization_rate' => $utilizationRate,
            'downtime_rate' => $downtimeRate,
            'failed_booking_rate' => $failedBookingRate,
            'charger_wait_minutes' => $chargerWaitMinutes,
            'range_risk_incidents' => $rangeRiskIncidents,
        ];
    }
}

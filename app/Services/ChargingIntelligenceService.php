<?php

namespace App\Services;

use App\Models\ChargingStation;
use App\Models\Vehicle;
use Illuminate\Support\Collection;

class ChargingIntelligenceService
{
    /**
     * @return Collection<int, ChargingStation>
     */
    public function rankForVehicle(Vehicle $vehicle, int $limit = 4): Collection
    {
        $stations = ChargingStation::query()
            ->where('connector_type', $vehicle->connector_type)
            ->get();

        return $this->attachSignals($stations)
            ->sortByDesc('ranking_score')
            ->take($limit)
            ->values();
    }

    /**
     * @param  Collection<int, ChargingStation>  $stations
     * @return Collection<int, ChargingStation>
     */
    public function attachSignals(Collection $stations): Collection
    {
        return $stations->each(function (ChargingStation $station): void {
            $liveAvailability = $station->live_availability ?? $station->available_ports;
            $score = ($station->confidence_score * 3)
                + ($liveAvailability * 16)
                + ($station->power_kw * 0.4)
                - ((float) $station->distance_from_hub_km * 7)
                - ((float) $station->price_per_kwh * 0.8);

            if ($station->operational_status === 'offline') {
                $score -= 300;
            } elseif ($station->operational_status === 'constrained') {
                $score -= 80;
            }

            if ($station->is_partner_hub) {
                $score += 40;
            }

            $riskLabel = match (true) {
                $station->operational_status === 'offline' || $station->confidence_score < 55 => 'Offline risk',
                $liveAvailability < 2 || $station->operational_status === 'constrained' => 'Limited ports',
                default => 'High confidence',
            };

            $station->setAttribute('live_ports', $liveAvailability);
            $station->setAttribute('ranking_score', round($score, 1));
            $station->setAttribute('risk_label', $riskLabel);
            $station->setAttribute('confidence_summary', match ($riskLabel) {
                'Offline risk' => 'Use only as contingency. Availability or connectivity confidence is weak.',
                'Limited ports' => 'Compatible and reachable, but queue pressure is likely.',
                default => 'Preferred station for lower wait time and stronger charge confidence.',
            });
        });
    }

    /**
     * @param  Collection<int, Vehicle>  $vehicles
     * @return Collection<int, array<string, string>>
     */
    public function relocationRecommendations(Collection $vehicles): Collection
    {
        return $vehicles
            ->filter(fn (Vehicle $vehicle) => $vehicle->battery_soc < 35 && $vehicle->status !== 'charging')
            ->take(3)
            ->map(function (Vehicle $vehicle): ?array {
                $recommendedStation = $this->rankForVehicle($vehicle, 5)
                    ->first(fn (ChargingStation $station) => $station->is_partner_hub)
                    ?? $this->rankForVehicle($vehicle, 1)->first();

                if (! $recommendedStation) {
                    return null;
                }

                return [
                    'title' => "Relocate {$vehicle->name} toward {$recommendedStation->zone}",
                    'copy' => "Battery is at {$vehicle->battery_soc}%. Move this vehicle to {$recommendedStation->name} to preserve hub coverage and reduce dispatch risk.",
                    'label' => $recommendedStation->risk_label,
                ];
            })
            ->filter()
            ->values();
    }
}

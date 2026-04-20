<?php

namespace App\Services;

use App\Models\Vehicle;
use Carbon\CarbonInterface;

class BookingEstimator
{
    /**
     * @return array<string, float|int>
     */
    public function estimate(Vehicle $vehicle, CarbonInterface $startAt, CarbonInterface $endAt, int $distanceKm): array
    {
        $durationHours = max(1, (int) ceil($startAt->diffInMinutes($endAt) / 60));
        $rentalDays = max(1, (int) ceil($durationHours / 24));

        $safeRangeKm = max($vehicle->estimated_range_km - 25, 0);
        $distanceShare = min($distanceKm / max($vehicle->estimated_range_km, 1), 1);
        $availableEnergy = (float) $vehicle->battery_capacity_kwh * ($vehicle->battery_soc / 100);
        $estimatedEnergyKwh = round($availableEnergy * $distanceShare, 1);
        $projectedReturnSoc = max(0, (int) round($vehicle->battery_soc * (1 - $distanceShare)));

        $baseCost = round((float) $vehicle->daily_rate * $rentalDays, 2);
        $distanceCost = round((float) $vehicle->per_km_rate * $distanceKm, 2);
        $energyCost = round((float) $vehicle->energy_rate * $estimatedEnergyKwh, 2);
        $batteryWearCost = round($estimatedEnergyKwh * 0.65, 2);

        return [
            'rental_days' => $rentalDays,
            'duration_hours' => $durationHours,
            'safe_range_km' => $safeRangeKm,
            'estimated_energy_kwh' => $estimatedEnergyKwh,
            'projected_return_soc' => $projectedReturnSoc,
            'base_cost' => $baseCost,
            'distance_cost' => $distanceCost,
            'energy_cost' => $energyCost,
            'battery_wear_cost' => $batteryWearCost,
            'total_cost' => round($baseCost + $distanceCost + $energyCost + $batteryWearCost, 2),
        ];
    }
}

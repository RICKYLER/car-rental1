<?php

namespace App\Services;

use App\Models\SecurityEvent;
use App\Models\Vehicle;
use App\Models\VehicleTelemetry;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TelematicsService
{
    public const LIVE_WINDOW_MINUTES = 2;

    public const ESTIMATED_WINDOW_MINUTES = 5;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function recordLiveSnapshot(Vehicle $vehicle, array $payload): VehicleTelemetry
    {
        $observedAt = $payload['observed_at'] ?? now();

        return $this->persistSnapshot($vehicle, [
            ...$payload,
            'observed_at' => $observedAt,
            'received_at' => $payload['received_at'] ?? $observedAt,
            'connectivity_status' => $payload['connectivity_status'] ?? 'live',
            'battery_source' => $payload['battery_source'] ?? 'live',
            'sync_delay_seconds' => (int) ($payload['sync_delay_seconds'] ?? 0),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function syncBufferedSnapshot(Vehicle $vehicle, array $payload, CarbonInterface $observedAt): VehicleTelemetry
    {
        $receivedAt = now();

        return $this->persistSnapshot($vehicle, [
            ...$payload,
            'observed_at' => $observedAt,
            'received_at' => $receivedAt,
            'connectivity_status' => 'buffered',
            'battery_source' => $payload['battery_source'] ?? 'buffered',
            'sync_delay_seconds' => abs((int) $receivedAt->diffInSeconds($observedAt, false)),
        ]);
    }

    /**
     * @param  iterable<Vehicle>  $vehicles
     * @return Collection<int, Vehicle>
     */
    public function attachSummaries(iterable $vehicles): Collection
    {
        return collect($vehicles)->each(function (Vehicle $vehicle): void {
            $vehicle->setAttribute('telemetry_summary', $this->present($vehicle));
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function present(Vehicle $vehicle): array
    {
        $telemetry = $vehicle->latestTelemetry;
        $observedAt = $telemetry?->observed_at ?? $vehicle->telematics_observed_at ?? $vehicle->last_seen_at;
        $connectivity = $telemetry?->connectivity_status ?? $vehicle->connectivity_status ?? 'offline';
        $batterySource = $telemetry?->battery_source ?? $vehicle->battery_source ?? 'estimated';
        $syncDelaySeconds = $telemetry?->sync_delay_seconds ?? $vehicle->sync_delay_seconds ?? 0;
        $positionAccuracy = $telemetry?->position_accuracy_m ?? $vehicle->position_accuracy_m;
        $batterySoc = $telemetry?->battery_soc ?? $vehicle->battery_soc;
        $rangeKm = $telemetry?->estimated_range_km ?? $vehicle->estimated_range_km;
        $ageMinutes = $observedAt ? max(0, now()->diffInRealMinutes($observedAt, false) * -1) : null;

        $freshness = match (true) {
            ! $observedAt,
            $connectivity === 'offline',
            $ageMinutes !== null && $ageMinutes > self::ESTIMATED_WINDOW_MINUTES => 'stale',
            $connectivity === 'buffered',
            $batterySource !== 'live',
            $syncDelaySeconds > 30,
            $ageMinutes !== null && $ageMinutes > self::LIVE_WINDOW_MINUTES => 'estimated',
            default => 'live',
        };

        $rangeConfidenceMultiplier = match ($freshness) {
            'estimated' => 0.92,
            'stale' => 0.82,
            default => 1,
        };

        $confidenceRangeKm = (int) max(0, round($rangeKm * $rangeConfidenceMultiplier));

        return [
            'observed_at' => $observedAt,
            'connectivity_status' => $connectivity,
            'battery_source' => $batterySource,
            'sync_delay_seconds' => $syncDelaySeconds,
            'position_accuracy_m' => $positionAccuracy,
            'battery_soc' => $batterySoc,
            'range_km' => $confidenceRangeKm,
            'raw_range_km' => $rangeKm,
            'freshness' => $freshness,
            'freshness_label' => ucfirst($freshness),
            'signal_label' => match ($connectivity) {
                'buffered' => 'Recovered via delayed sync',
                'offline' => 'Signal offline',
                default => 'Live signal',
            },
            'confidence_tone' => match ($freshness) {
                'stale' => 'danger',
                'estimated' => 'warn',
                default => 'good',
            },
            'summary' => match ($freshness) {
                'stale' => 'Telemetry is stale. Routing and range are being estimated until signal returns.',
                'estimated' => 'Recent dead-zone buffering detected. Range and charger guidance are using the synced snapshot.',
                default => 'Live telemetry feed is current enough for real-time trip confidence.',
            },
        ];
    }

    /**
     * @param  Collection<int, Vehicle>  $vehicles
     * @return Collection<int, array<string, mixed>>
     */
    public function impossibleJumpAlerts(Collection $vehicles): Collection
    {
        return $vehicles->map(function (Vehicle $vehicle): ?array {
            $recentTelemetry = $vehicle->telematics
                ->sortByDesc('observed_at')
                ->take(2)
                ->values();

            if ($recentTelemetry->count() < 2) {
                return null;
            }

            /** @var VehicleTelemetry $current */
            $current = $recentTelemetry[0];
            /** @var VehicleTelemetry $previous */
            $previous = $recentTelemetry[1];

            if (
                $current->gps_latitude === null || $current->gps_longitude === null ||
                $previous->gps_latitude === null || $previous->gps_longitude === null
            ) {
                return null;
            }

            $distanceKm = $this->distanceKm(
                $previous->gps_latitude,
                $previous->gps_longitude,
                $current->gps_latitude,
                $current->gps_longitude,
            );

            $minutes = max(1, $previous->observed_at->diffInMinutes($current->observed_at));
            $speedKph = ($distanceKm / $minutes) * 60;

            if ($distanceKm < 30 || $speedKph < 180) {
                return null;
            }

            SecurityEvent::query()->firstOrCreate(
                [
                    'event_type' => 'impossible_gps_jump',
                    'description' => "{$vehicle->name} reported an impossible GPS jump.",
                    'detected_at' => $current->observed_at,
                ],
                [
                    'severity' => 'critical',
                    'result_status' => 'detected',
                    'user_id' => null,
                    'actor_email' => null,
                    'ip_address' => null,
                    'metadata' => [
                        'vehicle_id' => $vehicle->id,
                        'distance_km' => round($distanceKm, 1),
                        'speed_kph' => round($speedKph, 1),
                    ],
                ],
            );

            return [
                'level' => 'Critical',
                'title' => "{$vehicle->name} reported an impossible route jump",
                'copy' => 'Latest telemetry implies '.round($speedKph).' km/h across '.round($distanceKm, 1).' km. Review tracking integrity before dispatch.',
            ];
        })->filter()->values();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function persistSnapshot(Vehicle $vehicle, array $payload): VehicleTelemetry
    {
        $telemetry = $vehicle->telematics()->create([
            'observed_at' => $payload['observed_at'],
            'received_at' => $payload['received_at'],
            'connectivity_status' => $payload['connectivity_status'],
            'battery_source' => $payload['battery_source'],
            'position_accuracy_m' => $payload['position_accuracy_m'] ?? null,
            'sync_delay_seconds' => $payload['sync_delay_seconds'],
            'battery_soc' => $payload['battery_soc'],
            'estimated_range_km' => $payload['estimated_range_km'],
            'gps_latitude' => $payload['gps_latitude'] ?? null,
            'gps_longitude' => $payload['gps_longitude'] ?? null,
            'notes' => $payload['notes'] ?? null,
        ]);

        $vehicle->forceFill([
            'battery_soc' => $payload['battery_soc'],
            'estimated_range_km' => $payload['estimated_range_km'],
            'gps_latitude' => $payload['gps_latitude'] ?? $vehicle->gps_latitude,
            'gps_longitude' => $payload['gps_longitude'] ?? $vehicle->gps_longitude,
            'last_seen_at' => $payload['received_at'],
            'telematics_observed_at' => $payload['observed_at'],
            'connectivity_status' => $payload['connectivity_status'],
            'position_accuracy_m' => $payload['position_accuracy_m'] ?? $vehicle->position_accuracy_m,
            'battery_source' => $payload['battery_source'],
            'sync_delay_seconds' => $payload['sync_delay_seconds'],
        ])->save();

        $vehicle->setRelation('latestTelemetry', $telemetry);

        return $telemetry;
    }

    private function distanceKm(float $latA, float $lngA, float $latB, float $lngB): float
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($latB - $latA);
        $lngDelta = deg2rad($lngB - $lngA);
        $sinLat = sin($latDelta / 2);
        $sinLng = sin($lngDelta / 2);

        $a = $sinLat ** 2 + cos(deg2rad($latA)) * cos(deg2rad($latB)) * $sinLng ** 2;

        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}

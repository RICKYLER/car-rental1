<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\ChargingSession;
use App\Models\ChargingStation;
use App\Models\SecurityEvent;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\RemoteCommandService;
use App\Services\TelematicsService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EcrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SecurityEvent::query()->delete();
        SystemSetting::query()->delete();
        ChargingSession::query()->delete();
        Booking::query()->delete();
        Vehicle::query()->delete();
        ChargingStation::query()->delete();
        User::query()->whereIn('email', [
            'ops.manager@ecros.test',
            'dane@ecros.test',
            'mavy@ecros.test',
            'commuter@ecros.test',
        ])->delete();

        SystemSetting::putValue('v2g_enabled', false, 'boolean');

        User::query()->create([
            'name' => 'Lowell Orcullo',
            'email' => 'ops.manager@ecros.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0917-555-0101',
            'preferred_zone' => 'Operations Hub',
            'license_verified' => true,
        ]);

        $customers = collect([
            [
                'name' => 'Dane Rodilla',
                'email' => 'dane@ecros.test',
                'phone' => '0917-555-0102',
                'preferred_zone' => 'North Business District',
            ],
            [
                'name' => 'Mavy Balaga',
                'email' => 'mavy@ecros.test',
                'phone' => '0917-555-0103',
                'preferred_zone' => 'Greenline Terminal',
            ],
            [
                'name' => 'Isla Rivera',
                'email' => 'commuter@ecros.test',
                'phone' => '0917-555-0104',
                'preferred_zone' => 'Seaside Loop',
            ],
        ])->map(fn (array $customer) => User::query()->create([
            ...$customer,
            'password' => Hash::make('password'),
            'role' => 'customer',
            'license_verified' => true,
        ]));

        $stations = collect([
            [
                'name' => 'Greenline Terminal Charger',
                'location' => 'Greenline Transport Hub',
                'zone' => 'Greenline Terminal',
                'connector_type' => 'CCS2',
                'total_ports' => 6,
                'available_ports' => 2,
                'live_availability' => 2,
                'price_per_kwh' => 13.50,
                'distance_from_hub_km' => 1.4,
                'status' => 'online',
                'power_kw' => 120,
                'operational_status' => 'online',
                'confidence_score' => 92,
                'is_partner_hub' => true,
                'latitude' => 14.5995000,
                'longitude' => 120.9842000,
            ],
            [
                'name' => 'North District Fast Charge',
                'location' => 'North Business District',
                'zone' => 'North Business District',
                'connector_type' => 'CCS2',
                'total_ports' => 4,
                'available_ports' => 1,
                'live_availability' => 1,
                'price_per_kwh' => 12.80,
                'distance_from_hub_km' => 2.1,
                'status' => 'online',
                'power_kw' => 150,
                'operational_status' => 'online',
                'confidence_score' => 88,
                'is_partner_hub' => true,
                'latitude' => 14.6112000,
                'longitude' => 121.0215000,
            ],
            [
                'name' => 'Seaside Loop AC Point',
                'location' => 'Seaside Loop',
                'zone' => 'Seaside Loop',
                'connector_type' => 'Type 2',
                'total_ports' => 8,
                'available_ports' => 5,
                'live_availability' => 1,
                'price_per_kwh' => 11.40,
                'distance_from_hub_km' => 3.9,
                'status' => 'busy',
                'power_kw' => 22,
                'operational_status' => 'constrained',
                'confidence_score' => 64,
                'is_partner_hub' => false,
                'latitude' => 14.5660000,
                'longitude' => 120.9918000,
            ],
        ])->map(fn (array $station) => ChargingStation::query()->create($station));

        $vehicles = collect([
            [
                'name' => 'Nissan Leaf City',
                'brand' => 'Nissan',
                'model' => 'Leaf',
                'year' => 2024,
                'plate_number' => 'ECR-101',
                'status' => 'available',
                'battery_soc' => 86,
                'battery_health' => 95,
                'estimated_range_km' => 248,
                'battery_capacity_kwh' => 62.0,
                'connector_type' => 'CCS2',
                'location_zone' => 'North Business District',
                'daily_rate' => 2450.00,
                'per_km_rate' => 7.50,
                'energy_rate' => 12.80,
                'odometer_km' => 18240,
                'description' => 'Compact EV tuned for short urban loops, student drivers, and business district pickups.',
                'accent_color' => '#20c997',
                'last_seen_at' => now()->subMinutes(2),
                'last_service_at' => now()->subDays(17),
                'next_service_due_at' => now()->addDays(45),
                'gps_latitude' => 14.6112000,
                'gps_longitude' => 121.0215000,
            ],
            [
                'name' => 'Hyundai Kona Long Range',
                'brand' => 'Hyundai',
                'model' => 'Kona Electric',
                'year' => 2025,
                'plate_number' => 'ECR-102',
                'status' => 'available',
                'battery_soc' => 74,
                'battery_health' => 97,
                'estimated_range_km' => 305,
                'battery_capacity_kwh' => 64.0,
                'connector_type' => 'CCS2',
                'location_zone' => 'Greenline Terminal',
                'daily_rate' => 2890.00,
                'per_km_rate' => 8.10,
                'energy_rate' => 13.50,
                'odometer_km' => 9450,
                'description' => 'Longer-range EV for airport transfers, all-day rentals, and higher confidence dispatching.',
                'accent_color' => '#4dabf7',
                'last_seen_at' => now()->subMinutes(4),
                'last_service_at' => now()->subDays(28),
                'next_service_due_at' => now()->addDays(23),
                'gps_latitude' => 14.5995000,
                'gps_longitude' => 120.9842000,
            ],
            [
                'name' => 'BYD Dolphin Metro',
                'brand' => 'BYD',
                'model' => 'Dolphin',
                'year' => 2025,
                'plate_number' => 'ECR-103',
                'status' => 'charging',
                'battery_soc' => 28,
                'battery_health' => 96,
                'estimated_range_km' => 118,
                'battery_capacity_kwh' => 44.9,
                'connector_type' => 'CCS2',
                'location_zone' => 'Greenline Terminal',
                'daily_rate' => 2180.00,
                'per_km_rate' => 6.90,
                'energy_rate' => 13.50,
                'odometer_km' => 12410,
                'description' => 'Efficient city EV currently charging after a morning booking cycle.',
                'accent_color' => '#74c0fc',
                'last_seen_at' => now()->subMinute(),
                'last_service_at' => now()->subDays(10),
                'next_service_due_at' => now()->addDays(52),
                'gps_latitude' => 14.5995000,
                'gps_longitude' => 120.9842000,
            ],
            [
                'name' => 'Tesla Model 3 Executive',
                'brand' => 'Tesla',
                'model' => 'Model 3',
                'year' => 2024,
                'plate_number' => 'ECR-104',
                'status' => 'reserved',
                'battery_soc' => 63,
                'battery_health' => 94,
                'estimated_range_km' => 280,
                'battery_capacity_kwh' => 60.0,
                'connector_type' => 'CCS2',
                'location_zone' => 'Seaside Loop',
                'daily_rate' => 3450.00,
                'per_km_rate' => 9.40,
                'energy_rate' => 12.30,
                'odometer_km' => 22190,
                'description' => 'Premium EV reserved for executive trips and longer point-to-point bookings.',
                'accent_color' => '#ffd43b',
                'last_seen_at' => now()->subMinutes(7),
                'last_service_at' => now()->subDays(41),
                'next_service_due_at' => now()->addDays(8),
                'gps_latitude' => 14.5660000,
                'gps_longitude' => 120.9918000,
            ],
            [
                'name' => 'MG4 Campus Shuttle',
                'brand' => 'MG',
                'model' => 'MG4',
                'year' => 2025,
                'plate_number' => 'ECR-105',
                'status' => 'maintenance',
                'battery_soc' => 91,
                'battery_health' => 82,
                'estimated_range_km' => 310,
                'battery_capacity_kwh' => 64.0,
                'connector_type' => 'Type 2',
                'location_zone' => 'Operations Hub',
                'daily_rate' => 2590.00,
                'per_km_rate' => 7.10,
                'energy_rate' => 11.40,
                'odometer_km' => 31220,
                'description' => 'Undergoing preventive maintenance after repeated high-load campus shuttle duty.',
                'accent_color' => '#ff8787',
                'last_seen_at' => now()->subMinutes(14),
                'last_service_at' => now()->subDays(93),
                'next_service_due_at' => now()->addDays(3),
                'gps_latitude' => 14.6098000,
                'gps_longitude' => 121.0403000,
            ],
        ])->map(fn (array $vehicle) => Vehicle::query()->create($vehicle));

        foreach ([
            [
                'reference' => 'ECROS-ACT101',
                'user_id' => $customers[0]->id,
                'vehicle_id' => $vehicles[3]->id,
                'status' => 'active',
                'pickup_location' => 'Seaside Loop',
                'dropoff_location' => 'North Business District',
                'start_at' => Carbon::now()->subHours(3),
                'end_at' => Carbon::now()->addHours(5),
                'estimated_distance_km' => 82,
                'estimated_energy_kwh' => 17.6,
                'projected_return_soc' => 44,
                'base_cost' => 3450.00,
                'distance_cost' => 770.80,
                'energy_cost' => 216.48,
                'battery_wear_cost' => 11.44,
                'total_cost' => 4448.72,
                'license_verified' => true,
                'notes' => 'Executive intercity meeting run.',
            ],
            [
                'reference' => 'ECROS-CNF202',
                'user_id' => $customers[1]->id,
                'vehicle_id' => $vehicles[1]->id,
                'status' => 'confirmed',
                'pickup_location' => 'Greenline Terminal',
                'dropoff_location' => 'Seaside Loop',
                'start_at' => Carbon::now()->addHours(10),
                'end_at' => Carbon::now()->addDays(1)->addHours(4),
                'estimated_distance_km' => 120,
                'estimated_energy_kwh' => 19.0,
                'projected_return_soc' => 45,
                'base_cost' => 5780.00,
                'distance_cost' => 972.00,
                'energy_cost' => 256.50,
                'battery_wear_cost' => 12.35,
                'total_cost' => 7020.85,
                'license_verified' => true,
                'notes' => 'Weekend coastal route with charging fallback enabled.',
            ],
            [
                'reference' => 'ECROS-CMP303',
                'user_id' => $customers[2]->id,
                'vehicle_id' => $vehicles[0]->id,
                'status' => 'completed',
                'pickup_location' => 'North Business District',
                'dropoff_location' => 'Greenline Terminal',
                'start_at' => Carbon::now()->subDays(2),
                'end_at' => Carbon::now()->subDays(2)->addHours(14),
                'estimated_distance_km' => 96,
                'estimated_energy_kwh' => 20.3,
                'projected_return_soc' => 37,
                'base_cost' => 2450.00,
                'distance_cost' => 720.00,
                'energy_cost' => 259.84,
                'battery_wear_cost' => 13.20,
                'total_cost' => 3443.04,
                'license_verified' => true,
                'notes' => 'Completed same-day loop for a university event.',
            ],
        ] as $booking) {
            Booking::query()->create($booking);
        }

        ChargingSession::query()->create([
            'vehicle_id' => $vehicles[2]->id,
            'charging_station_id' => $stations[0]->id,
            'status' => 'in_progress',
            'started_at' => Carbon::now()->subMinutes(35),
            'expected_completion_at' => Carbon::now()->addMinutes(48),
            'current_soc' => 28,
            'target_soc' => 85,
            'energy_kwh' => 25.6,
            'estimated_cost' => 345.60,
        ]);

        ChargingSession::query()->create([
            'vehicle_id' => $vehicles[3]->id,
            'charging_station_id' => $stations[1]->id,
            'status' => 'scheduled',
            'expected_completion_at' => Carbon::now()->addHours(8),
            'current_soc' => 44,
            'target_soc' => 90,
            'energy_kwh' => 27.6,
            'estimated_cost' => 353.28,
        ]);

        $telematics = app(TelematicsService::class);
        $telematics->recordLiveSnapshot($vehicles[0], [
            'observed_at' => now()->subMinutes(2),
            'battery_soc' => 86,
            'estimated_range_km' => 248,
            'position_accuracy_m' => 11,
            'gps_latitude' => 14.6112000,
            'gps_longitude' => 121.0215000,
            'notes' => 'Urban telemetry feed healthy.',
        ]);
        $telematics->syncBufferedSnapshot($vehicles[1], [
            'battery_soc' => 74,
            'estimated_range_km' => 291,
            'position_accuracy_m' => 24,
            'gps_latitude' => 14.6029000,
            'gps_longitude' => 120.9886000,
            'notes' => 'Recovered after tunnel dead zone.',
        ], now()->subMinutes(4));
        $telematics->recordLiveSnapshot($vehicles[2], [
            'observed_at' => now()->subMinute(),
            'battery_soc' => 28,
            'estimated_range_km' => 118,
            'position_accuracy_m' => 8,
            'gps_latitude' => 14.5995000,
            'gps_longitude' => 120.9842000,
            'notes' => 'Charging bay telemetry nominal.',
        ]);
        $telematics->recordLiveSnapshot($vehicles[3], [
            'observed_at' => now()->subMinutes(9),
            'battery_soc' => 63,
            'estimated_range_km' => 280,
            'position_accuracy_m' => 14,
            'gps_latitude' => 14.5660000,
            'gps_longitude' => 120.9918000,
            'notes' => 'Previous trusted route point.',
        ]);
        $telematics->syncBufferedSnapshot($vehicles[3], [
            'battery_soc' => 61,
            'estimated_range_km' => 258,
            'position_accuracy_m' => 41,
            'gps_latitude' => 15.0451000,
            'gps_longitude' => 121.8075000,
            'notes' => 'Buffered sync after route discontinuity.',
        ], now()->subMinutes(4));
        $telematics->recordLiveSnapshot($vehicles[4], [
            'observed_at' => now()->subMinutes(16),
            'received_at' => now()->subMinutes(8),
            'connectivity_status' => 'offline',
            'battery_source' => 'estimated',
            'sync_delay_seconds' => 480,
            'battery_soc' => 91,
            'estimated_range_km' => 248,
            'position_accuracy_m' => 85,
            'gps_latitude' => 14.6098000,
            'gps_longitude' => 121.0403000,
            'notes' => 'Maintenance unit offline in a signal dead zone.',
        ]);

        SecurityEvent::query()->create([
            'actor_email' => 'unknown.ops@invalid.test',
            'event_type' => 'failed_login',
            'severity' => 'watch',
            'result_status' => 'blocked',
            'ip_address' => '10.24.18.11',
            'description' => 'Rejected login attempt.',
            'metadata' => ['seeded' => true],
            'detected_at' => now()->subMinutes(40),
        ]);
        SecurityEvent::query()->create([
            'actor_email' => 'unknown.ops@invalid.test',
            'event_type' => 'failed_login',
            'severity' => 'watch',
            'result_status' => 'blocked',
            'ip_address' => '10.24.18.11',
            'description' => 'Rejected login attempt.',
            'metadata' => ['seeded' => true],
            'detected_at' => now()->subMinutes(26),
        ]);
        SecurityEvent::query()->create([
            'actor_email' => 'unknown.ops@invalid.test',
            'event_type' => 'failed_login',
            'severity' => 'critical',
            'result_status' => 'blocked',
            'ip_address' => '10.24.18.11',
            'description' => 'Rejected login attempt.',
            'metadata' => ['seeded' => true],
            'detected_at' => now()->subMinutes(14),
        ]);

        $remoteCommands = app(RemoteCommandService::class);
        $admin = User::query()->where('email', 'ops.manager@ecros.test')->firstOrFail();
        $remoteCommands->issue($admin, $vehicles[3], 'unlock', 'Contactless executive handover.', true, '127.0.0.1');
        $remoteCommands->issue($admin, $vehicles[2], 'immobilize', 'Rejected without step-up verification.', false, '127.0.0.1');
    }
}

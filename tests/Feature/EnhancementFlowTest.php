<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\TelematicsService;
use Database\Seeders\EcrosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnhancementFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EcrosSeeder::class);
    }

    public function test_buffered_telematics_sync_records_delayed_snapshot_history(): void
    {
        $vehicle = Vehicle::query()->where('name', 'Nissan Leaf City')->firstOrFail();
        $initialTelemetryCount = $vehicle->telematics()->count();

        app(TelematicsService::class)->syncBufferedSnapshot($vehicle, [
            'battery_soc' => 82,
            'estimated_range_km' => 226,
            'position_accuracy_m' => 33,
            'gps_latitude' => 14.6151000,
            'gps_longitude' => 121.0302000,
            'notes' => 'Recovered after a parking-structure dead zone.',
        ], now()->subMinutes(6));

        $vehicle->refresh();
        $latestBufferedSnapshot = $vehicle->telematics()
            ->latest('created_at')
            ->firstOrFail();

        $this->assertSame($initialTelemetryCount + 1, $vehicle->telematics()->count());
        $this->assertSame('buffered', $latestBufferedSnapshot->connectivity_status);
        $this->assertGreaterThan(0, $latestBufferedSnapshot->sync_delay_seconds);
        $this->assertSame(82, $vehicle->battery_soc);
    }

    public function test_booking_is_rejected_when_projected_return_soc_is_below_minimum_floor(): void
    {
        $customer = User::query()->where('email', 'dane@ecros.test')->firstOrFail();
        $vehicle = Vehicle::query()->where('name', 'Nissan Leaf City')->firstOrFail();
        $existingCount = Booking::query()->count();

        $response = $this->actingAs($customer)->from('/bookings/create')->post('/bookings', [
            'vehicle_id' => $vehicle->id,
            'pickup_location' => $vehicle->location_zone,
            'dropoff_location' => 'South Harbor',
            'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'estimated_distance_km' => 210,
        ]);

        $response->assertRedirect('/bookings/create');
        $response->assertSessionHasErrors('estimated_distance_km');
        $this->assertSame($existingCount, Booking::query()->count());
    }

    public function test_admin_remote_command_requires_step_up_verification_and_logs_rejection(): void
    {
        $admin = User::query()->where('email', 'ops.manager@ecros.test')->firstOrFail();
        $vehicle = Vehicle::query()->where('name', 'BYD Dolphin Metro')->firstOrFail();

        $response = $this->actingAs($admin)->from('/admin')->post('/admin/remote-commands', [
            'vehicle_id' => $vehicle->id,
            'command_type' => 'immobilize',
            'justification' => 'Suspicious dispatch attempt.',
            'current_password' => 'wrong-password',
        ]);

        $response->assertRedirect('/admin');
        $response->assertSessionHasErrors('current_password');

        $this->assertDatabaseHas('remote_commands', [
            'vehicle_id' => $vehicle->id,
            'command_type' => 'immobilize',
            'result_status' => 'rejected',
        ]);
        $this->assertDatabaseHas('security_events', [
            'event_type' => 'remote_command_rejected',
            'result_status' => 'blocked',
        ]);
    }

    public function test_admin_can_execute_remote_unlock_and_admin_dashboard_shows_simulation_panel(): void
    {
        $admin = User::query()->where('email', 'ops.manager@ecros.test')->firstOrFail();
        $vehicle = Vehicle::query()->where('name', 'Nissan Leaf City')->firstOrFail();

        $this->actingAs($admin)->post('/admin/remote-commands', [
            'vehicle_id' => $vehicle->id,
            'command_type' => 'unlock',
            'justification' => 'Approved handover.',
            'current_password' => 'password',
        ])->assertRedirect();

        $vehicle->refresh();

        $this->assertFalse($vehicle->is_locked);
        $this->assertDatabaseHas('remote_commands', [
            'vehicle_id' => $vehicle->id,
            'command_type' => 'unlock',
            'result_status' => 'executed',
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Simulation lab')
            ->assertSee('Peak-hour demand')
            ->assertSee('Signed lock, unlock, and immobilize commands');
    }
}

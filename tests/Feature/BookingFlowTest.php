<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\EcrosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EcrosSeeder::class);
    }

    public function test_customer_only_sees_their_own_bookings(): void
    {
        $customer = User::query()->where('email', 'dane@ecros.test')->firstOrFail();

        $this->actingAs($customer)
            ->get('/bookings')
            ->assertOk()
            ->assertSee('ECROS-ACT101')
            ->assertDontSee('ECROS-CNF202');
    }

    public function test_customer_can_create_booking_for_available_vehicle(): void
    {
        $customer = User::query()->where('email', 'dane@ecros.test')->firstOrFail();
        $vehicle = Vehicle::query()->where('status', 'available')->firstOrFail();

        $response = $this->actingAs($customer)->post('/bookings', [
            'vehicle_id' => $vehicle->id,
            'pickup_location' => $vehicle->location_zone,
            'dropoff_location' => 'City Science Park',
            'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'estimated_distance_km' => 70,
            'notes' => 'Demo booking from test suite.',
        ]);

        $response->assertRedirect('/bookings');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_location' => $vehicle->location_zone,
            'dropoff_location' => 'City Science Park',
        ]);

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'status' => 'reserved',
        ]);
    }

    public function test_booking_is_rejected_when_trip_exceeds_safe_range(): void
    {
        $customer = User::query()->where('email', 'dane@ecros.test')->firstOrFail();
        $vehicle = Vehicle::query()->where('name', 'Nissan Leaf City')->firstOrFail();
        $existingCount = Booking::query()->count();

        $response = $this->actingAs($customer)->from('/bookings/create')->post('/bookings', [
            'vehicle_id' => $vehicle->id,
            'pickup_location' => $vehicle->location_zone,
            'dropoff_location' => 'Far Province',
            'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'estimated_distance_km' => 500,
        ]);

        $response->assertRedirect('/bookings/create');
        $response->assertSessionHasErrors('estimated_distance_km');

        $this->assertSame($existingCount, Booking::query()->count());
    }

    public function test_admin_can_view_all_bookings_but_cannot_open_customer_booking_form(): void
    {
        $admin = User::query()->where('email', 'ops.manager@ecros.test')->firstOrFail();

        $this->actingAs($admin)
            ->get('/bookings')
            ->assertOk()
            ->assertSee('ECROS-ACT101')
            ->assertSee('ECROS-CNF202');

        $this->actingAs($admin)
            ->get('/bookings/create')
            ->assertForbidden();
    }
}

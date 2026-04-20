<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Database\Seeders\EcrosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcrosPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EcrosSeeder::class);
    }

    public function test_overview_page_loads_ecros_content(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Find a charged EV, book fast, and keep every trip feeling premium.')
            ->assertSee('Featured EVs with strong charge and clean availability');
    }

    public function test_fleet_page_lists_seeded_vehicle(): void
    {
        $this->get('/fleet')
            ->assertOk()
            ->assertSee('Nissan Leaf City')
            ->assertSee('Choose the EV that fits your route, battery needs, and pickup zone.');
    }

    public function test_vehicle_detail_page_remains_public(): void
    {
        $vehicle = Vehicle::query()->firstOrFail();

        $this->get("/fleet/{$vehicle->id}")
            ->assertOk()
            ->assertSee($vehicle->name)
            ->assertSee('Compatible stations nearby');
    }

    public function test_guests_are_redirected_from_protected_pages(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/bookings')->assertRedirect('/login');
        $this->get('/bookings/create')->assertRedirect('/login');
        $this->get('/admin')->assertRedirect('/login');
    }
}

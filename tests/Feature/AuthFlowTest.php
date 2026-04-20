<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\EcrosSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EcrosSeeder::class);
    }

    public function test_customer_can_log_in_and_log_out(): void
    {
        $response = $this->post('/login', [
            'email' => 'dane@ecros.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_can_log_in_and_access_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'ops.manager@ecros.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Admin telemetry, charging, and fleet readiness in a cleaner control surface.')
            ->assertSee('Vehicle health and readiness');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::query()->where('email', 'dane@ecros.test')->firstOrFail();

        $this->actingAs($customer)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Fresh Customer',
            'email' => 'fresh.customer@example.com',
            'phone' => '0917-555-0200',
            'preferred_zone' => 'Central Hub',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'fresh.customer@example.com',
            'role' => 'customer',
            'preferred_zone' => 'Central Hub',
            'license_verified' => false,
        ]);
    }

    public function test_customer_can_request_reset_link_and_reset_password(): void
    {
        Notification::fake();

        $user = User::query()->where('email', 'dane@ecros.test')->firstOrFail();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);

        $token = Password::broker()->createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect('/login');

        $this->assertCredentials([
            'email' => $user->email,
            'password' => 'NewPassword123!',
        ]);
    }
}

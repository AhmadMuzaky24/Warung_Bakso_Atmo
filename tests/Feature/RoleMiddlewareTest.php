<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk();
    }

    public function test_staff_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk();
    }

    public function test_customer_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk();
    }

    public function test_inactive_user_is_logged_out(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => false,
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_unknown_role_is_forbidden(): void
    {
        $user = User::factory()->create([
            'role' => 'unknown',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertForbidden();
    }
}
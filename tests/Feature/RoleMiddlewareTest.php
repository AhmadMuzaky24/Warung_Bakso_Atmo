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
            'role' => 'admin', // SESUAIKAN dengan isi file lamamu (nama kolom dan nilai)
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_staff_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_customer_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_inactive_user_is_logged_out(): void
    {
        $user = User::factory()->create([
            'role'      => 'admin',
            'is_active' => false, // SESUAIKAN: nama kolom status nonaktif di Sprint 1
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_unknown_role_is_forbidden(): void
    {
        $user = User::factory()->create([
            'role' => 'unknown',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertForbidden();
    }
}
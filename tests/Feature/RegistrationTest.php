<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // throttle:10,1 dihitung per IP; seluruh kelas test jalan dari IP yang sama.
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Customer Baru',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_guest_can_open_register_form_without_email_field(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertDontSee('name="email"', false);
    }

    public function test_guest_can_register_with_phone_and_is_logged_in(): void
    {
        $this->post('/register', $this->payload())
            ->assertRedirect(route('customer.dashboard'));

        $user = User::sole();

        $this->assertSame('081234567890', $user->phone);
        $this->assertSame('081234567890@pelanggan.baksoatmo.local', $user->email);
        $this->assertSame('customer', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertAuthenticatedAs($user);
    }

    public function test_international_format_is_normalized_to_local(): void
    {
        $this->post('/register', $this->payload(['phone' => '+6281234567890']))
            ->assertRedirect(route('customer.dashboard'));

        $this->assertSame('081234567890', User::sole()->phone);
    }

    public function test_62_prefix_is_normalized_to_local(): void
    {
        $this->post('/register', $this->payload(['phone' => '6281234567890']))
            ->assertRedirect(route('customer.dashboard'));

        $this->assertSame('081234567890', User::sole()->phone);
    }

    public function test_invalid_phone_numbers_are_rejected(): void
    {
        $invalid = ['12345', '0812345', '08123456789012', '+1234567890', '0812-abc-7890', '+62'];

        foreach ($invalid as $phone) {
            $this->post('/register', $this->payload(['phone' => $phone]))
                ->assertSessionHasErrors('phone');
        }

        $this->assertSame(0, User::count());
    }

    public function test_duplicate_phone_across_formats_is_rejected(): void
    {
        $this->post('/register', $this->payload())->assertRedirect();

        auth()->logout();

        $this->post('/register', $this->payload(['phone' => '+62 812-3456-7890']))
            ->assertSessionHasErrors('phone');

        $this->assertSame(1, User::count());
    }

    public function test_role_from_payload_is_ignored(): void
    {
        $this->post('/register', $this->payload([
            'role' => 'admin',
            'is_active' => false,
        ]))->assertRedirect();

        $user = User::sole();

        $this->assertSame('customer', $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_password_rules_are_enforced(): void
    {
        $this->post('/register', $this->payload(['password' => 'pendek', 'password_confirmation' => 'pendek']))
            ->assertSessionHasErrors('password');

        $this->post('/register', $this->payload(['password_confirmation' => 'beda-sekali']))
            ->assertSessionHasErrors('password');

        $this->assertSame(0, User::count());
    }

    public function test_authenticated_user_is_redirected_away_from_register(): void
    {
        $user = User::create([
            'name' => 'Sudah Login',
            'phone' => '081200000001',
            'email' => 'sudah@login.test',
            'password' => 'password123',
            'role' => 'customer',
            'is_active' => true,
        ]);

        $this->actingAs($user)->get('/register')->assertRedirect();
    }

    public function test_register_redirects_back_to_intended_url(): void
    {
        // Guest yang klik "Pesan Katering" disimpan sebagai intended URL oleh middleware auth.
        $this->get('/customer/catering/create')->assertRedirect('/login');

        $this->post('/register', $this->payload())
            ->assertRedirect('/customer/catering/create');

        $this->assertAuthenticated();
    }

    public function test_customer_can_log_in_with_phone_after_register(): void
    {
        $this->post('/register', $this->payload())->assertRedirect();

        auth()->logout();

        $this->post('/login', [
            'login' => '081234567890',
            'password' => 'password123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs(User::sole());
    }

    public function test_customer_can_log_in_with_international_phone_format(): void
    {
        $this->post('/register', $this->payload())->assertRedirect();

        auth()->logout();

        $this->post('/login', [
            'login' => '+62 812-3456-7890',
            'password' => 'password123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs(User::sole());
    }

    public function test_dashboard_shows_phone_instead_of_synthetic_email(): void
    {
        $this->post('/register', $this->payload())->assertRedirect();

        $this->actingAs(User::sole())->get('/dashboard')
            ->assertOk()
            ->assertSee('081234567890')
            ->assertDontSee('@pelanggan.baksoatmo.local');

        // Jalur admin/staff tidak boleh ikut berubah: akun tanpa phone tetap tampil emailnya.
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password123',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get('/dashboard')
            ->assertOk()
            ->assertSee($admin->email);
    }

    public function test_login_with_uppercase_email_is_case_insensitive(): void
    {
        $staff = User::create([
            'name' => 'Staff Case',
            'phone' => '081200000003',
            'email' => 'staff.case@example.test',
            'password' => 'password123',
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->post('/login', [
            'login' => 'Staff.Case@Example.Test',
            'password' => 'password123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($staff);
    }

    public function test_staff_can_still_log_in_with_email(): void
    {
        $staff = User::create([
            'name' => 'Staff',
            'phone' => '081200000002',
            'email' => 'staff@example.test',
            'password' => 'password123',
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->post('/login', [
            'login' => 'staff@example.test',
            'password' => 'password123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($staff);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CateringOrder;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_staff_and_customer_cannot_access_admin_dashboard(): void
    {
        foreach (['staff', 'customer'] as $role) {
            $user = User::factory()->create(['role' => $role, 'is_active' => true]);

            $this->actingAs($user)
                ->get('/admin/dashboard')
                ->assertForbidden();
        }
    }

    public function test_admin_dashboard_displays_accurate_summaries_and_recent_orders(): void
    {
        $admin = $this->createAdmin();
        $customer = User::factory()->create(['role' => 'customer', 'name' => 'Pelanggan Uji Dashboard']);

        $category = Category::factory()->create();
        Menu::factory()->count(2)->create(['category_id' => $category->id, 'is_available' => true]);
        Menu::factory()->create(['category_id' => $category->id, 'is_available' => false]);

        CateringOrder::factory()->create([
            'booking_code' => 'BA-20261126-9001',
            'user_id' => $customer->id,
            'event_date' => now()->toDateString(),
            'total_amount' => 500000,
            'dp_amount' => 250000,
            'status' => CateringOrder::STATUS_SUBMITTED,
            'payment_status' => CateringOrder::PAYMENT_PENDING,
        ]);

        CateringOrder::factory()->create([
            'booking_code' => 'BA-20261126-9002',
            'user_id' => $customer->id,
            'event_date' => now()->addDays(3)->toDateString(),
            'total_amount' => 800000,
            'dp_amount' => 400000,
            'status' => CateringOrder::STATUS_COMPLETED,
            'payment_status' => CateringOrder::PAYMENT_VERIFIED,
        ]);

        CateringOrder::factory()->create([
            'booking_code' => 'BA-20261126-9003',
            'user_id' => $customer->id,
            'event_date' => now()->addDays(4)->toDateString(),
            'total_amount' => 300000,
            'dp_amount' => 150000,
            'status' => CateringOrder::STATUS_REJECTED,
            'payment_status' => CateringOrder::PAYMENT_REJECTED,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Rp 1.300.000') // activeRevenue (500.000 + 800.000, mengecualikan rejected)
            ->assertSee('Rp 400.000') // verifiedDp
            ->assertSee('Rp 250.000') // pendingDp
            ->assertSee('2 / 3 Menu') // 2 available / 3 total
            ->assertSee('BA-20261126-9001')
            ->assertSee('BA-20261126-9002')
            ->assertSee(route('admin.catering.show', CateringOrder::where('booking_code', 'BA-20261126-9001')->firstOrFail()), false);
    }

    public function test_admin_navigation_links_to_admin_dashboard_and_reports(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee(route('admin.reports.index'), false);

        $this->withoutVite()
            ->actingAs($admin)
            ->get('/')
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false);
    }
}

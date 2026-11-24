<?php

namespace Tests\Feature;

use App\Models\CateringOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class StaffCateringOperationsTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    private CateringOrder $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->create(['role' => 'staff']);
        $this->order = CateringOrder::factory()->create([
            'booking_code' => 'BA-20261124-0001',
            'status' => CateringOrder::STATUS_SUBMITTED,
            'payment_status' => CateringOrder::PAYMENT_PENDING,
        ]);
    }

    // ---- Akses role ----

    public function test_guest_is_redirected_to_login_on_every_staff_endpoint(): void
    {
        $this->get('/staff/dashboard')->assertRedirect('/login');
        $this->get('/staff/catering')->assertRedirect('/login');
        $this->get('/staff/catering/'.$this->order->id)->assertRedirect('/login');
        $this->patch('/staff/catering/'.$this->order->id.'/status', ['status' => 'processing'])
            ->assertRedirect('/login');
    }

    public function test_admin_and_customer_cannot_access_staff_endpoints(): void
    {
        foreach (['admin', 'customer'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get('/staff/dashboard')->assertForbidden();
            $this->actingAs($user)->get('/staff/catering')->assertForbidden();
            $this->actingAs($user)->get('/staff/catering/'.$this->order->id)->assertForbidden();
            $this->actingAs($user)
                ->patch('/staff/catering/'.$this->order->id.'/status', ['status' => 'processing'])
                ->assertForbidden();
        }

        $this->assertSame(CateringOrder::STATUS_SUBMITTED, $this->order->fresh()->status);
    }

    // ---- Halaman Staff ----

    public function test_staff_can_view_dashboard_list_and_detail(): void
    {
        $this->actingAs($this->staff)
            ->get('/staff/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Staff')
            ->assertSee('BA-20261124-0001');

        $this->actingAs($this->staff)
            ->get('/staff/catering')
            ->assertOk()
            ->assertSee('BA-20261124-0001');

        $this->actingAs($this->staff)
            ->get('/staff/catering/'.$this->order->id)
            ->assertOk()
            ->assertSee('BA-20261124-0001')
            ->assertSee($this->order->event_address);
    }

    public function test_staff_pages_show_status_and_payment_status_separately(): void
    {
        $this->order->update([
            'status' => CateringOrder::STATUS_PROCESSING,
            'payment_status' => CateringOrder::PAYMENT_VERIFIED,
        ]);

        $this->actingAs($this->staff)
            ->get('/staff/catering/'.$this->order->id)
            ->assertSee('Status pesanan')
            ->assertSee('Status pembayaran')
            ->assertSee('badge-processing', false)
            ->assertSee('badge-verified', false);
    }

    public function test_staff_detail_does_not_render_payment_status_form(): void
    {
        $this->actingAs($this->staff)
            ->get('/staff/catering/'.$this->order->id)
            ->assertDontSee('name="payment_status"', false);
    }

    // ---- Perubahan status ----

    public function test_staff_can_run_the_full_status_flow(): void
    {
        foreach ([
            CateringOrder::STATUS_PROCESSING,
            CateringOrder::STATUS_COMPLETED,
        ] as $status) {
            $this->actingAs($this->staff)
                ->patch('/staff/catering/'.$this->order->id.'/status', ['status' => $status])
                ->assertRedirect(route('staff.catering.show', $this->order))
                ->assertSessionHas('success');

            $this->assertSame($status, $this->order->fresh()->status);
        }
    }

    public function test_staff_can_reject_an_order(): void
    {
        $this->actingAs($this->staff)
            ->patch('/staff/catering/'.$this->order->id.'/status', ['status' => CateringOrder::STATUS_REJECTED])
            ->assertRedirect();

        $this->assertSame(CateringOrder::STATUS_REJECTED, $this->order->fresh()->status);
    }

    public function test_staff_cannot_set_a_status_outside_the_allowed_values(): void
    {
        $this->actingAs($this->staff)
            ->from('/staff/catering/'.$this->order->id)
            ->patch('/staff/catering/'.$this->order->id.'/status', ['status' => 'verified'])
            ->assertSessionHasErrors('status');

        $this->actingAs($this->staff)
            ->patch('/staff/catering/'.$this->order->id.'/status', [])
            ->assertSessionHasErrors('status');

        $this->assertSame(CateringOrder::STATUS_SUBMITTED, $this->order->fresh()->status);
    }

    // ---- Pemisahan wewenang: Staff vs payment_status ----

    public function test_server_rejects_staff_request_that_carries_payment_status(): void
    {
        $this->actingAs($this->staff)
            ->patch('/staff/catering/'.$this->order->id.'/status', [
                'status' => CateringOrder::STATUS_PROCESSING,
                'payment_status' => CateringOrder::PAYMENT_VERIFIED,
            ])
            ->assertForbidden();

        $fresh = $this->order->fresh();
        $this->assertSame(CateringOrder::STATUS_SUBMITTED, $fresh->status);
        $this->assertSame(CateringOrder::PAYMENT_PENDING, $fresh->payment_status);
    }

    public function test_server_rejects_staff_request_with_only_payment_status(): void
    {
        $this->actingAs($this->staff)
            ->patch('/staff/catering/'.$this->order->id.'/status', [
                'payment_status' => CateringOrder::PAYMENT_VERIFIED,
            ])
            ->assertForbidden();

        $this->assertSame(CateringOrder::PAYMENT_PENDING, $this->order->fresh()->payment_status);
    }

    public function test_staff_cannot_reach_the_admin_payment_endpoint(): void
    {
        $this->actingAs($this->staff)
            ->patch('/admin/catering/'.$this->order->id.'/payment-status', [
                'payment_status' => CateringOrder::PAYMENT_VERIFIED,
            ])
            ->assertForbidden();

        $this->assertSame(CateringOrder::PAYMENT_PENDING, $this->order->fresh()->payment_status);
    }

    public function test_staff_area_has_no_route_that_can_change_payment_status(): void
    {
        $staffRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'staff/'));

        $this->assertNotEmpty($staffRoutes);

        foreach ($staffRoutes as $route) {
            $this->assertStringNotContainsString('payment', $route->uri());
            $this->assertStringNotContainsString('payment', (string) $route->getName());
            $this->assertStringNotContainsString('payment', (string) $route->getActionName());
        }
    }

    // ---- Customer hanya membaca kedua status pesanannya sendiri ----

    public function test_customer_reads_both_statuses_on_own_order_and_has_no_edit_endpoint(): void
    {
        $customer = $this->order->user;
        $this->order->update([
            'status' => CateringOrder::STATUS_PROCESSING,
            'payment_status' => CateringOrder::PAYMENT_VERIFIED,
        ]);

        $this->actingAs($customer)
            ->get('/customer/catering/'.$this->order->id)
            ->assertOk()
            ->assertSee('Diproses')
            ->assertSee('DP terverifikasi');

        $this->actingAs($customer)
            ->patch('/staff/catering/'.$this->order->id.'/status', ['status' => 'completed'])
            ->assertForbidden();
        $this->actingAs($customer)
            ->patch('/admin/catering/'.$this->order->id.'/payment-status', ['payment_status' => 'verified'])
            ->assertForbidden();
        $this->actingAs($customer)
            ->patch('/customer/catering/'.$this->order->id, ['status' => 'completed'])
            ->assertStatus(405);

        $fresh = $this->order->fresh();
        $this->assertSame(CateringOrder::STATUS_PROCESSING, $fresh->status);
        $this->assertSame(CateringOrder::PAYMENT_VERIFIED, $fresh->payment_status);
    }
}

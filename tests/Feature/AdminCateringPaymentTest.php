<?php

namespace Tests\Feature;

use App\Models\CateringOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminCateringPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private CateringOrder $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->order = CateringOrder::factory()->create([
            'booking_code' => 'BA-20261124-0002',
            'status' => CateringOrder::STATUS_PROCESSING,
            'payment_status' => CateringOrder::PAYMENT_PENDING,
        ]);
    }

    // ---- Akses role ----

    public function test_guest_is_redirected_to_login_on_every_admin_catering_endpoint(): void
    {
        $this->get('/admin/catering')->assertRedirect('/login');
        $this->get('/admin/catering/'.$this->order->id)->assertRedirect('/login');
        $this->patch('/admin/catering/'.$this->order->id.'/payment-status', ['payment_status' => 'verified'])
            ->assertRedirect('/login');
    }

    public function test_staff_and_customer_cannot_access_admin_catering_endpoints(): void
    {
        foreach (['staff', 'customer'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get('/admin/catering')->assertForbidden();
            $this->actingAs($user)->get('/admin/catering/'.$this->order->id)->assertForbidden();
            $this->actingAs($user)
                ->patch('/admin/catering/'.$this->order->id.'/payment-status', ['payment_status' => 'verified'])
                ->assertForbidden();
        }

        $this->assertSame(CateringOrder::PAYMENT_PENDING, $this->order->fresh()->payment_status);
    }

    // ---- Halaman Admin ----

    public function test_admin_can_view_all_orders_and_detail(): void
    {
        $other = CateringOrder::factory()->create(['booking_code' => 'BA-20261124-0003']);

        $this->actingAs($this->admin)
            ->get('/admin/catering')
            ->assertOk()
            ->assertSee('BA-20261124-0002')
            ->assertSee($other->booking_code);

        $this->actingAs($this->admin)
            ->get('/admin/catering/'.$this->order->id)
            ->assertOk()
            ->assertSee('BA-20261124-0002')
            ->assertSee('Verifikasi Pembayaran');
    }

    public function test_admin_pages_show_status_and_payment_status_separately(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/catering/'.$this->order->id)
            ->assertSee('Status pesanan')
            ->assertSee('Status pembayaran')
            ->assertSee('badge-processing', false)
            ->assertSee('badge-pending', false);
    }

    public function test_admin_detail_does_not_render_order_status_form(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/catering/'.$this->order->id)
            ->assertDontSee('name="status"', false);
    }

    // ---- Perubahan payment_status ----

    public function test_admin_can_verify_and_reject_payment(): void
    {
        foreach ([CateringOrder::PAYMENT_VERIFIED, CateringOrder::PAYMENT_REJECTED] as $paymentStatus) {
            $this->actingAs($this->admin)
                ->patch('/admin/catering/'.$this->order->id.'/payment-status', ['payment_status' => $paymentStatus])
                ->assertRedirect(route('admin.catering.show', $this->order))
                ->assertSessionHas('success');

            $this->assertSame($paymentStatus, $this->order->fresh()->payment_status);
        }

        // status pesanan tidak ikut berubah
        $this->assertSame(CateringOrder::STATUS_PROCESSING, $this->order->fresh()->status);
    }

    public function test_admin_cannot_set_a_payment_status_outside_the_allowed_values(): void
    {
        $this->actingAs($this->admin)
            ->patch('/admin/catering/'.$this->order->id.'/payment-status', ['payment_status' => 'completed'])
            ->assertSessionHasErrors('payment_status');

        $this->actingAs($this->admin)
            ->patch('/admin/catering/'.$this->order->id.'/payment-status', [])
            ->assertSessionHasErrors('payment_status');

        $this->assertSame(CateringOrder::PAYMENT_PENDING, $this->order->fresh()->payment_status);
    }

    // ---- Pemisahan wewenang: Admin vs status pesanan ----

    public function test_server_rejects_admin_request_that_carries_order_status(): void
    {
        $this->actingAs($this->admin)
            ->patch('/admin/catering/'.$this->order->id.'/payment-status', [
                'payment_status' => CateringOrder::PAYMENT_VERIFIED,
                'status' => CateringOrder::STATUS_COMPLETED,
            ])
            ->assertForbidden();

        $fresh = $this->order->fresh();
        $this->assertSame(CateringOrder::STATUS_PROCESSING, $fresh->status);
        $this->assertSame(CateringOrder::PAYMENT_PENDING, $fresh->payment_status);
    }

    public function test_admin_cannot_reach_the_staff_status_endpoint(): void
    {
        $this->actingAs($this->admin)
            ->patch('/staff/catering/'.$this->order->id.'/status', ['status' => CateringOrder::STATUS_COMPLETED])
            ->assertForbidden();

        $this->assertSame(CateringOrder::STATUS_PROCESSING, $this->order->fresh()->status);
    }

    public function test_admin_catering_routes_cannot_update_order_status(): void
    {
        $writeRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'admin/catering'))
            ->filter(fn ($route) => array_diff($route->methods(), ['GET', 'HEAD']) !== []);

        $this->assertCount(1, $writeRoutes);
        $this->assertStringContainsString('payment-status', $writeRoutes->first()->uri());

        // Tidak ada endpoint lain di area admin yang menerima perubahan status.
        $this->actingAs($this->admin)
            ->patch('/admin/catering/'.$this->order->id, ['status' => CateringOrder::STATUS_COMPLETED])
            ->assertStatus(405);
        $this->actingAs($this->admin)
            ->put('/admin/catering/'.$this->order->id.'/status', ['status' => CateringOrder::STATUS_COMPLETED])
            ->assertNotFound();

        $this->assertSame(CateringOrder::STATUS_PROCESSING, $this->order->fresh()->status);
    }
}

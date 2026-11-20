<?php

namespace Tests\Feature;

use App\Models\CateringOrder;
use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use App\Services\CateringBookingService;
use App\Support\BookingWindow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCateringBookingTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private Category $category;

    private Menu $menu;

    private Menu $unavailableMenu;

    protected function setUp(): void
    {
        parent::setUp();

        // Waktu dibekukan: helper payload() dipakai hampir semua test, jangan bergantung jam suite.
        $this->travelTo(\Carbon\Carbon::parse('2026-11-20 09:00:00'));

        $this->category = Category::create(['name' => 'Mie Ayam']);

        $this->menu = Menu::create([
            'category_id' => $this->category->id,
            'name' => 'Mie Ayam Spesial',
            'price' => 25000,
            'is_available' => true,
        ]);

        $this->unavailableMenu = Menu::create([
            'category_id' => $this->category->id,
            'name' => 'Menu Habis',
            'price' => 20000,
            'is_available' => false,
        ]);

        $this->customer = User::create([
            'name' => 'Customer Satu',
            'email' => 'customer1@example.test',
            'password' => 'password',
            'role' => 'customer',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        $this->travelBack();

        parent::tearDown();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'event_date' => BookingWindow::earliestEventDate()->toDateString(),
            'event_address' => 'Jl. Manggis No. 2, Karawang Barat',
            'customer_phone' => '081234567890',
            'notes' => 'Tanpa sambal',
            'items' => [
                ['menu_id' => $this->menu->id, 'quantity' => 10],
            ],
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/customer/catering/create')->assertRedirect('/login');
    }

    public function test_customer_can_store_booking_with_items(): void
    {
        $this->actingAs($this->customer)
            ->post('/customer/catering', $this->payload())
            ->assertRedirect();

        $order = CateringOrder::sole();

        $this->assertSame($this->customer->id, $order->user_id);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(250000, $order->total_amount);
    }

    public function test_server_ignores_client_supplied_totals_and_code(): void
    {
        $this->actingAs($this->customer)
            ->post('/customer/catering', $this->payload([
                'total_amount' => 1,
                'dp_amount' => 1,
                'booking_code' => 'HACKED-0001',
                'status' => 'completed',
                'payment_status' => 'verified',
                'user_id' => 999,
            ]))
            ->assertRedirect();

        $order = CateringOrder::sole();

        $this->assertSame(250000, $order->total_amount);
        $this->assertSame(125000, $order->dp_amount);
        $this->assertNotSame('HACKED-0001', $order->booking_code);
        $this->assertSame(CateringOrder::STATUS_SUBMITTED, $order->status);
        $this->assertSame(CateringOrder::PAYMENT_PENDING, $order->payment_status);
        $this->assertSame($this->customer->id, $order->user_id);
    }

    public function test_booking_code_and_initial_status(): void
    {
        $this->actingAs($this->customer)->post('/customer/catering', $this->payload());

        $order = CateringOrder::sole();

        $this->assertMatchesRegularExpression('/^BA-\d{8}-\d{4}$/', $order->booking_code);
        $this->assertSame('submitted', $order->status);
        $this->assertSame('pending', $order->payment_status);
    }

    public function test_dp_amount_is_dp_percent_of_total(): void
    {
        $this->actingAs($this->customer)->post('/customer/catering', $this->payload([
            'items' => [['menu_id' => $this->menu->id, 'quantity' => 7]],
        ]));

        $order = CateringOrder::sole();
        $percent = (int) config('bakso_atmo.booking.dp_percent', 50);

        $this->assertSame(175000, $order->total_amount);
        $this->assertSame((int) round(175000 * $percent / 100), $order->dp_amount);
    }

    public function test_booking_before_cutoff_can_still_target_tomorrow(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-20 13:00:00'));

        $this->actingAs($this->customer)
            ->post('/customer/catering', $this->payload(['event_date' => '2026-11-21']))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, CateringOrder::count());
    }

    public function test_booking_exactly_at_cutoff_still_counts(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-20 14:30:00'));

        $this->actingAs($this->customer)
            ->post('/customer/catering', $this->payload(['event_date' => '2026-11-21']))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, CateringOrder::count());
    }

    public function test_booking_one_second_after_cutoff_is_rejected(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-20 14:30:01'));

        $this->actingAs($this->customer)
            ->post('/customer/catering', $this->payload(['event_date' => '2026-11-21']))
            ->assertSessionHasErrors('event_date');

        $this->assertSame(0, CateringOrder::count());
    }

    public function test_booking_after_cutoff_must_target_the_day_after_tomorrow(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-20 17:00:00'));

        $this->actingAs($this->customer)
            ->post('/customer/catering', $this->payload(['event_date' => '2026-11-21']))
            ->assertSessionHasErrors('event_date');

        $this->actingAs($this->customer)
            ->post('/customer/catering', $this->payload(['event_date' => '2026-11-22']))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, CateringOrder::count());
    }

    public function test_same_day_event_date_is_always_rejected(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-20 08:00:00'));

        $this->actingAs($this->customer)
            ->post('/customer/catering', $this->payload(['event_date' => '2026-11-20']))
            ->assertSessionHasErrors('event_date');

        $this->assertSame(0, CateringOrder::count());
    }

    public function test_unavailable_menu_is_rejected(): void
    {
        $this->actingAs($this->customer)
            ->post('/customer/catering', $this->payload([
                'items' => [['menu_id' => $this->unavailableMenu->id, 'quantity' => 5]],
            ]))
            ->assertSessionHasErrors('items.0.menu_id');

        $this->assertSame(0, CateringOrder::count());
        $this->assertSame(0, \App\Models\CateringOrderItem::count());
    }

    public function test_item_snapshot_survives_menu_edit(): void
    {
        $this->actingAs($this->customer)->post('/customer/catering', $this->payload());

        $item = CateringOrder::sole()->items()->sole();

        $this->menu->update(['name' => 'Nama Baru', 'price' => 99000]);

        $item->refresh();

        $this->assertSame('Mie Ayam Spesial', $item->menu_name);
        $this->assertSame(25000, $item->unit_price);
        $this->assertSame(250000, $item->subtotal);
    }

    public function test_whatsapp_url_contains_booking_code(): void
    {
        config()->set('bakso_atmo.whatsapp', '628111222333');

        $this->actingAs($this->customer)->post('/customer/catering', $this->payload());

        $order = CateringOrder::sole();
        $url = app(CateringBookingService::class)->generateWhatsAppUrl($order);

        $this->assertIsString($url);
        $this->assertStringStartsWith('https://wa.me/628111222333', $url);
        $this->assertStringContainsString($order->booking_code, $url);
    }

    public function test_whatsapp_url_is_null_and_page_still_renders_when_number_missing(): void
    {
        config()->set('bakso_atmo.whatsapp', null);

        $this->actingAs($this->customer)->post('/customer/catering', $this->payload());

        $order = CateringOrder::sole();

        $this->assertNull(app(CateringBookingService::class)->generateWhatsAppUrl($order));

        $this->actingAs($this->customer)
            ->get('/customer/catering/'.$order->id)
            ->assertOk()
            ->assertSee('Nomor WhatsApp belum dikonfigurasi');
    }
}

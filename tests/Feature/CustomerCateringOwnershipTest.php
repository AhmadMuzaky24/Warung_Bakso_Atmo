<?php

namespace Tests\Feature;

use App\Models\CateringOrder;
use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCateringOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $other;

    private Menu $menu;

    private CateringOrder $order;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Mie Ayam']);

        $this->menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Mie Ayam Spesial',
            'price' => 25000,
            'is_available' => true,
        ]);

        $this->owner = User::create([
            'name' => 'Owner',
            'email' => 'owner@example.test',
            'password' => 'password',
            'role' => 'customer',
            'is_active' => true,
        ]);

        $this->other = User::create([
            'name' => 'Other',
            'email' => 'other@example.test',
            'password' => 'password',
            'role' => 'customer',
            'is_active' => true,
        ]);

        $this->order = CateringOrder::create([
            'booking_code' => 'BA-20261116-0001',
            'user_id' => $this->owner->id,
            'event_date' => now()->addDays(2)->toDateString(),
            'event_address' => 'Jl. Manggis No. 2',
            'customer_phone' => '081234567890',
            'total_amount' => 250000,
            'dp_amount' => 125000,
            'status' => CateringOrder::STATUS_SUBMITTED,
            'payment_status' => CateringOrder::PAYMENT_PENDING,
        ]);

        $this->order->items()->create([
            'menu_id' => $this->menu->id,
            'menu_name' => 'Mie Ayam Spesial',
            'unit_price' => 25000,
            'quantity' => 10,
            'subtotal' => 250000,
        ]);
    }

    public function test_customer_cannot_view_another_customer_order(): void
    {
        $this->actingAs($this->other)
            ->get('/customer/catering/'.$this->order->id)
            ->assertForbidden();
    }

    public function test_customer_can_view_own_order(): void
    {
        $this->actingAs($this->owner)
            ->get('/customer/catering/'.$this->order->id)
            ->assertOk()
            ->assertSee($this->order->booking_code);
    }

    public function test_admin_cannot_access_customer_area(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get('/customer/catering')->assertForbidden();
        $this->actingAs($admin)->get('/customer/dashboard')->assertForbidden();

        $staff = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => 'password',
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->actingAs($staff)->get('/customer/dashboard')->assertForbidden();
    }

    public function test_customer_can_view_dashboard(): void
    {
        $this->actingAs($this->owner)
            ->get(route('customer.dashboard'))
            ->assertOk()
            ->assertSee($this->owner->name);
    }

    public function test_inactive_customer_is_logged_out_and_redirected_to_login(): void
    {
        $this->owner->update(['is_active' => false]);

        $this->actingAs($this->owner)
            ->get('/customer/catering')
            ->assertRedirect('/login');
    }

    public function test_index_only_lists_own_orders(): void
    {
        CateringOrder::create([
            'booking_code' => 'BA-20261116-0002',
            'user_id' => $this->other->id,
            'event_date' => now()->addDays(3)->toDateString(),
            'event_address' => 'Jl. Lain No. 9',
            'customer_phone' => '081298765432',
            'total_amount' => 50000,
            'dp_amount' => 25000,
            'status' => CateringOrder::STATUS_SUBMITTED,
            'payment_status' => CateringOrder::PAYMENT_PENDING,
        ]);

        $this->actingAs($this->owner)
            ->get('/customer/catering')
            ->assertOk()
            ->assertSee($this->order->booking_code)
            ->assertDontSee('BA-20261116-0002');
    }
}

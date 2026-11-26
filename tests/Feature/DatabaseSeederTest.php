<?php

namespace Tests\Feature;

use App\Models\CateringOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_test_accounts_and_at_least_30_catering_orders_with_items(): void
    {
        Storage::fake('public');

        $this->seed();
        // Jalankan ulang untuk memastikan seeder bersifat idempoten
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'admin.test@baksoatmo.local',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'staff.test@baksoatmo.local',
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'customer.test@baksoatmo.local',
            'role' => 'customer',
            'is_active' => true,
        ]);

        $this->assertGreaterThanOrEqual(
            3,
            User::query()->where('is_active', true)->whereIn('role', ['admin', 'staff', 'customer'])->count()
        );

        $orders = CateringOrder::with('items')->get();
        $this->assertGreaterThanOrEqual(30, $orders->count());

        foreach ($orders as $order) {
            $this->assertGreaterThanOrEqual(1, $order->items->count());
            $this->assertSame($order->items->sum('subtotal'), $order->total_amount);
        }
    }
}

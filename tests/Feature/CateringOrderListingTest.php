<?php

namespace Tests\Feature;

use App\Models\CateringOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Pencarian, filter, rentang tanggal, dan pagination 10 untuk daftar pesanan
 * Staff (/staff/catering) dan Admin (/admin/catering).
 */
class CateringOrderListingTest extends TestCase
{
    use RefreshDatabase;

    public static function areas(): array
    {
        return [
            'staff' => ['staff', '/staff/catering'],
            'admin' => ['admin', '/admin/catering'],
        ];
    }

    private function actor(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function order(array $attributes = [], string $customerName = 'Pelanggan Umum'): CateringOrder
    {
        return CateringOrder::factory()
            ->for(User::factory()->create(['name' => $customerName, 'role' => 'customer']), 'user')
            ->create($attributes);
    }

    #[DataProvider('areas')]
    public function test_search_by_booking_code(string $role, string $url): void
    {
        $this->order(['booking_code' => 'BA-20261124-0101']);
        $this->order(['booking_code' => 'BA-20261124-0202']);

        $this->actingAs($this->actor($role))
            ->get($url.'?search=0101')
            ->assertOk()
            ->assertSee('BA-20261124-0101')
            ->assertDontSee('BA-20261124-0202');
    }

    #[DataProvider('areas')]
    public function test_search_by_customer_name(string $role, string $url): void
    {
        $this->order(['booking_code' => 'BA-20261124-0301'], 'Siti Rahmawati');
        $this->order(['booking_code' => 'BA-20261124-0302'], 'Budi Santoso');

        $this->actingAs($this->actor($role))
            ->get($url.'?search=rahmawati')
            ->assertOk()
            ->assertSee('BA-20261124-0301')
            ->assertDontSee('BA-20261124-0302');
    }

    #[DataProvider('areas')]
    public function test_filter_by_status(string $role, string $url): void
    {
        $this->order(['booking_code' => 'BA-20261124-0401', 'status' => CateringOrder::STATUS_SUBMITTED]);
        $this->order(['booking_code' => 'BA-20261124-0402', 'status' => CateringOrder::STATUS_COMPLETED]);

        $this->actingAs($this->actor($role))
            ->get($url.'?status=completed')
            ->assertOk()
            ->assertSee('BA-20261124-0402')
            ->assertDontSee('BA-20261124-0401');
    }

    #[DataProvider('areas')]
    public function test_filter_by_payment_status(string $role, string $url): void
    {
        $this->order(['booking_code' => 'BA-20261124-0501', 'payment_status' => CateringOrder::PAYMENT_PENDING]);
        $this->order(['booking_code' => 'BA-20261124-0502', 'payment_status' => CateringOrder::PAYMENT_VERIFIED]);

        $this->actingAs($this->actor($role))
            ->get($url.'?payment_status=verified')
            ->assertOk()
            ->assertSee('BA-20261124-0502')
            ->assertDontSee('BA-20261124-0501');
    }

    #[DataProvider('areas')]
    public function test_filter_by_event_date_range(string $role, string $url): void
    {
        $this->order(['booking_code' => 'BA-20261124-0601', 'event_date' => '2026-12-01']);
        $this->order(['booking_code' => 'BA-20261124-0602', 'event_date' => '2026-12-15']);
        $this->order(['booking_code' => 'BA-20261124-0603', 'event_date' => '2026-12-31']);

        $this->actingAs($this->actor($role))
            ->get($url.'?start_date=2026-12-10&end_date=2026-12-20')
            ->assertOk()
            ->assertSee('BA-20261124-0602')
            ->assertDontSee('BA-20261124-0601')
            ->assertDontSee('BA-20261124-0603');

        // batas rentang bersifat inklusif
        $this->actingAs($this->actor($role))
            ->get($url.'?start_date=2026-12-01&end_date=2026-12-01')
            ->assertSee('BA-20261124-0601')
            ->assertDontSee('BA-20261124-0602');
    }

    #[DataProvider('areas')]
    public function test_filters_can_be_combined(string $role, string $url): void
    {
        $this->order([
            'booking_code' => 'BA-20261124-0701',
            'status' => CateringOrder::STATUS_PROCESSING,
            'event_date' => '2026-12-10',
        ], 'Dewi Lestari');
        $this->order([
            'booking_code' => 'BA-20261124-0702',
            'status' => CateringOrder::STATUS_PROCESSING,
            'event_date' => '2026-12-10',
        ], 'Agus Salim');
        $this->order([
            'booking_code' => 'BA-20261124-0703',
            'status' => CateringOrder::STATUS_SUBMITTED,
            'event_date' => '2026-12-10',
        ], 'Dewi Lestari');

        $this->actingAs($this->actor($role))
            ->get($url.'?search=Dewi&status=processing&start_date=2026-12-01&end_date=2026-12-31')
            ->assertSee('BA-20261124-0701')
            ->assertDontSee('BA-20261124-0702')
            ->assertDontSee('BA-20261124-0703');
    }

    #[DataProvider('areas')]
    public function test_invalid_filter_values_are_ignored_without_error(string $role, string $url): void
    {
        $this->order(['booking_code' => 'BA-20261124-0801']);

        $this->actingAs($this->actor($role))
            ->get($url.'?status=bukan-status&payment_status=xxx&start_date=bukan-tanggal&end_date=2026-99-99')
            ->assertOk()
            ->assertSee('BA-20261124-0801');

        $this->actingAs($this->actor($role))
            ->get($url.'?search[]=a&status[]=b')
            ->assertOk();
    }

    #[DataProvider('areas')]
    public function test_pagination_shows_ten_per_page_and_keeps_filters(string $role, string $url): void
    {
        foreach (range(1, 25) as $i) {
            $this->order(['booking_code' => sprintf('BA-20261124-9%03d', $i), 'status' => CateringOrder::STATUS_PROCESSING]);
        }
        $this->order(['booking_code' => 'BA-20261124-8000', 'status' => CateringOrder::STATUS_COMPLETED]);

        $actor = $this->actor($role);

        $page1 = $this->actingAs($actor)->get($url.'?status=processing');
        $page1->assertOk();
        $this->assertCount(10, $page1->viewData('orders')->items());
        $this->assertSame(25, $page1->viewData('orders')->total());
        $page1->assertDontSee('BA-20261124-8000');
        // link halaman berikutnya mempertahankan query filter
        $page1->assertSee('status=processing', false);
        $page1->assertSee('page=2', false);

        $page3 = $this->actingAs($actor)->get($url.'?status=processing&page=3');
        $this->assertCount(5, $page3->viewData('orders')->items());
    }
}

<?php

namespace Tests\Feature;

use App\Models\CateringOrder;
use App\Models\CateringOrderItem;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSpout\Reader\XLSX\Reader;
use Tests\TestCase;

class AdminReportExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_guest_is_redirected_to_login_on_report_and_export_endpoints(): void
    {
        $this->get('/admin/reports')->assertRedirect('/login');
        $this->get('/admin/reports/export')->assertRedirect('/login');
    }

    public function test_staff_and_customer_cannot_access_report_or_export_endpoints(): void
    {
        foreach (['staff', 'customer'] as $role) {
            $user = User::factory()->create(['role' => $role, 'is_active' => true]);

            $this->actingAs($user)->get('/admin/reports')->assertForbidden();
            $this->actingAs($user)->get('/admin/reports/export')->assertForbidden();
        }
    }

    public function test_admin_report_filters_by_inclusive_date_range_and_calculates_summary(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'name' => 'Pelanggan Laporan']);

        CateringOrder::factory()->for($customer, 'user')->create([
            'booking_code' => 'BA-20261201-0001',
            'event_date' => '2026-12-01',
            'total_amount' => 400000,
            'dp_amount' => 200000,
            'payment_status' => CateringOrder::PAYMENT_VERIFIED,
        ]);

        CateringOrder::factory()->for($customer, 'user')->create([
            'booking_code' => 'BA-20261215-0002',
            'event_date' => '2026-12-15',
            'total_amount' => 600000,
            'dp_amount' => 300000,
            'payment_status' => CateringOrder::PAYMENT_VERIFIED,
        ]);

        CateringOrder::factory()->for($customer, 'user')->create([
            'booking_code' => 'BA-20261231-0003',
            'event_date' => '2026-12-31',
            'total_amount' => 900000,
            'dp_amount' => 450000,
            'payment_status' => CateringOrder::PAYMENT_PENDING,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/reports?start_date=2026-12-01&end_date=2026-12-15');

        $response->assertOk()
            ->assertSee('BA-20261201-0001')
            ->assertSee('BA-20261215-0002')
            ->assertDontSee('BA-20261231-0003')
            ->assertSee('Rp 1.000.000') // total periode
            ->assertSee('Rp 500.000'); // total DP terverifikasi & sisa bayar
    }

    public function test_admin_report_handles_invalid_filters_and_preserves_pagination_query(): void
    {
        CateringOrder::factory()->count(15)->create([
            'event_date' => '2026-12-10',
            'status' => CateringOrder::STATUS_COMPLETED,
        ]);

        $this->actingAs($this->admin)
            ->get('/admin/reports?start_date=bukan-tanggal&status[]=invalid&payment_status=unknown')
            ->assertOk();

        $this->actingAs($this->admin)
            ->get('/admin/reports?start_date=2026-12-01&end_date=2026-12-31&status=completed')
            ->assertOk()
            ->assertSee('page=2', false)
            ->assertSee('status=completed', false);
    }

    public function test_admin_can_export_filtered_report_to_valid_xlsx_file(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'name' => 'Pelanggan Ekspor']);
        $menu = Menu::factory()->create(['name' => 'Bakso Urat Demo', 'price' => 25000]);

        $includedOrder = CateringOrder::factory()->for($customer, 'user')->create([
            'booking_code' => 'BA-20261210-0010',
            'event_date' => '2026-12-10',
            'total_amount' => 500000,
            'dp_amount' => 250000,
            'status' => CateringOrder::STATUS_COMPLETED,
            'payment_status' => CateringOrder::PAYMENT_VERIFIED,
        ]);

        CateringOrderItem::factory()->for($includedOrder, 'cateringOrder')->forMenu($menu, 20)->create();

        CateringOrder::factory()->for($customer, 'user')->create([
            'booking_code' => 'BA-20261225-0020',
            'event_date' => '2026-12-25',
            'total_amount' => 300000,
            'dp_amount' => 150000,
            'status' => CateringOrder::STATUS_SUBMITTED,
            'payment_status' => CateringOrder::PAYMENT_PENDING,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/reports/export?start_date=2026-12-01&end_date=2026-12-15');

        $response->assertOk()
            ->assertHeader(
                'Content-Type',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            );

        $filePath = $response->baseResponse->getFile()->getPathname();

        try {
            $reader = new Reader;
            $reader->open($filePath);

            $rows = [];
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rows[] = $row->toArray();
                }
            }
            $reader->close();
        } finally {
            @unlink($filePath);
        }

        $flattened = collect($rows)->flatten()->map(fn ($v) => (string) $v)->implode(' | ');

        $this->assertStringContainsString('BA-20261210-0010', $flattened);
        $this->assertStringContainsString('Bakso Urat Demo (20)', $flattened);
        $this->assertStringContainsString('Pelanggan Ekspor', $flattened);
        $this->assertStringNotContainsString('BA-20261225-0020', $flattened);
        $this->assertStringContainsString('TOTAL', $flattened);
    }
}

<?php

namespace Database\Seeders;

use App\Models\CateringOrder;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CateringOrderSeeder extends Seeder
{
    /**
     * Membuat minimal 30 pesanan katering demo beserta itemnya menggunakan data sintetis.
     */
    public function run(): void
    {
        $customers = User::query()
            ->where('role', 'customer')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $menus = Menu::query()
            ->where('is_available', true)
            ->orderBy('id')
            ->get();

        if ($customers->isEmpty() || $menus->isEmpty()) {
            return;
        }

        $dpPercent = (int) config('bakso_atmo.booking.dp_percent', 50);

        $statusMatrix = [
            [CateringOrder::STATUS_COMPLETED, CateringOrder::PAYMENT_VERIFIED],
            [CateringOrder::STATUS_COMPLETED, CateringOrder::PAYMENT_VERIFIED],
            [CateringOrder::STATUS_PROCESSING, CateringOrder::PAYMENT_VERIFIED],
            [CateringOrder::STATUS_PROCESSING, CateringOrder::PAYMENT_PENDING],
            [CateringOrder::STATUS_SUBMITTED, CateringOrder::PAYMENT_PENDING],
            [CateringOrder::STATUS_SUBMITTED, CateringOrder::PAYMENT_VERIFIED],
            [CateringOrder::STATUS_REJECTED, CateringOrder::PAYMENT_REJECTED],
            [CateringOrder::STATUS_COMPLETED, CateringOrder::PAYMENT_VERIFIED],
        ];

        $demoAddresses = [
            'Jl. Contoh Melati No. 10, Karawang Barat',
            'Perum Demo Asri Blok B2 No. 5, Karawang Timur',
            'Jl. Simulasi Raya No. 21, Nagasari, Karawang',
            'Aula Komunitas Demo 01, Telukjambe Timur',
            'Gedung Pertemuan Contoh, Galuh Mas, Karawang',
        ];

        $demoNotes = [
            'Mohon kuah dipisah saat pengantaran.',
            'Acara dimulai pukul 11.00 WIB.',
            'Tambahkan sambal dan kecap secukupnya.',
            null,
        ];

        $totalOrdersToSeed = 32;

        DB::transaction(function () use (
            $customers,
            $menus,
            $dpPercent,
            $statusMatrix,
            $demoAddresses,
            $demoNotes,
            $totalOrdersToSeed
        ): void {
            for ($i = 1; $i <= $totalOrdersToSeed; $i++) {
                $bookingCode = sprintf('BA-DEMO-%04d', $i);
                $customer = $customers[($i - 1) % $customers->count()];
                [$status, $paymentStatus] = $statusMatrix[($i - 1) % count($statusMatrix)];

                // Sebar tanggal acara dari -20 hari hingga +18 hari agar filter rentang tanggal kaya data.
                $dayOffset = $i - 20;
                $eventDate = now()->addDays($dayOffset)->toDateString();

                $itemCount = ($i % 3) + 1;
                $itemsPayload = [];
                $totalAmount = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $menu = $menus[($i + $j - 1) % $menus->count()];
                    $quantity = (($i + $j) % 4 + 1) * 5;
                    $unitPrice = (int) $menu->price;
                    $subtotal = $unitPrice * $quantity;

                    $itemsPayload[] = [
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ];

                    $totalAmount += $subtotal;
                }

                $dpAmount = (int) round($totalAmount * $dpPercent / 100);

                $order = CateringOrder::updateOrCreate(
                    ['booking_code' => $bookingCode],
                    [
                        'user_id' => $customer->id,
                        'event_date' => $eventDate,
                        'event_address' => $demoAddresses[($i - 1) % count($demoAddresses)],
                        'customer_phone' => $customer->phone ?: sprintf('08120000%04d', $i),
                        'notes' => $demoNotes[($i - 1) % count($demoNotes)],
                        'total_amount' => $totalAmount,
                        'dp_amount' => $dpAmount,
                        'status' => $status,
                        'payment_status' => $paymentStatus,
                    ]
                );

                $order->items()->delete();
                $order->items()->createMany($itemsPayload);
            }
        });
    }
}

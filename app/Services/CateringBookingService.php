<?php

namespace App\Services;

use App\Models\CateringOrder;
use App\Models\Menu;
use App\Models\User;
use App\Support\BookingWindow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CateringBookingService
{
    private int $dpPercent;

    public function __construct()
    {
        $this->dpPercent = (int) config('bakso_atmo.booking.dp_percent', 50);
    }

    public function validateEventDate(Carbon|string $date): bool
    {
        return BookingWindow::isDateAllowed($date);
    }

    /**
     * @param  array<int, array{menu_id:int, quantity:int}>  $lineItems
     * @return array{items: array<int, array{menu_id:int, menu_name:string, unit_price:int, quantity:int, subtotal:int}>, total_amount:int, dp_amount:int}
     */
    public function calculateTotals(array $lineItems): array
    {
        $ids = array_map(static fn (array $line): int => (int) $line['menu_id'], $lineItems);

        $menus = Menu::whereIn('id', $ids)->where('is_available', true)->get()->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($lineItems as $line) {
            $menu = $menus->get((int) $line['menu_id']);

            if (! $menu) {
                throw ValidationException::withMessages([
                    'items' => 'Menu yang dipilih tidak tersedia.',
                ]);
            }

            $quantity = (int) $line['quantity'];
            $subtotal = (int) $menu->price * $quantity;

            $items[] = [
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
                'unit_price' => (int) $menu->price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
        }

        return [
            'items' => $items,
            'total_amount' => $total,
            'dp_amount' => (int) round($total * $this->dpPercent / 100),
        ];
    }

    public function generateBookingCode(): string
    {
        $prefix = 'BA-'.now()->format('Ymd').'-';
        $seq = (int) CateringOrder::where('booking_code', 'like', $prefix.'%')->count();

        // ponytail: cek-keunikan 5x cukup untuk race praktis; ganti ke retry QueryException kalau traffic naik.
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $code = $prefix.str_pad((string) ($seq + $attempt), 4, '0', STR_PAD_LEFT);

            if (! CateringOrder::where('booking_code', $code)->exists()) {
                return $code;
            }
        }

        throw ValidationException::withMessages([
            'items' => 'Gagal membuat kode booking, silakan coba lagi.',
        ]);
    }

    public function createOrder(User $user, array $data): CateringOrder
    {
        return DB::transaction(function () use ($user, $data) {
            $calc = $this->calculateTotals($data['items']);

            $order = CateringOrder::create([
                'booking_code' => $this->generateBookingCode(),
                'user_id' => $user->id,
                'event_date' => $data['event_date'],
                'event_address' => $data['event_address'],
                'customer_phone' => $data['customer_phone'],
                'notes' => $data['notes'] ?? null,
                'total_amount' => $calc['total_amount'],
                'dp_amount' => $calc['dp_amount'],
                'status' => CateringOrder::STATUS_SUBMITTED,
                'payment_status' => CateringOrder::PAYMENT_PENDING,
            ]);

            foreach ($calc['items'] as $item) {
                $order->items()->create($item);
            }

            return $order->load('items');
        });
    }

    public function generateWhatsAppUrl(CateringOrder $order): ?string
    {
        $number = config('bakso_atmo.whatsapp');

        if (blank($number)) {
            return null;
        }

        $lines = [
            'Halo Warung Pak Atmo, saya ingin konfirmasi booking katering.',
            'Kode Booking: '.$order->booking_code,
            'Tanggal Acara: '.$order->event_date->format('d/m/Y'),
            'Alamat: '.$order->event_address,
            'Pesanan:',
        ];

        foreach ($order->items as $item) {
            $lines[] = '- '.$item->menu_name.' x '.$item->quantity;
        }

        $lines[] = 'Total: Rp '.number_format($order->total_amount, 0, ',', '.');
        $lines[] = 'DP '.$this->dpPercent.'%: Rp '.number_format($order->dp_amount, 0, ',', '.');

        return 'https://wa.me/'.preg_replace('/\D/', '', (string) $number)
            .'?text='.rawurlencode(implode("\n", $lines));
    }
}

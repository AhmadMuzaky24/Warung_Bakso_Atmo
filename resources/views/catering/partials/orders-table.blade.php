{{-- Tabel pesanan. Variabel: $orders (collection/paginator), $showRoute (nama route detail).
     status dan payment_status ditampilkan sebagai dua informasi terpisah. --}}
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Kode Booking</th>
                <th>Pelanggan</th>
                <th>Tanggal Acara</th>
                <th>Item</th>
                <th>Total</th>
                <th>Status Pesanan</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td><strong>{{ $order->booking_code }}</strong></td>
                    <td>
                        <div class="table-primary">{{ $order->user?->name ?? '-' }}</div>
                        <div class="table-secondary">{{ $order->customer_phone }}</div>
                    </td>
                    <td>{{ $order->event_date->locale('id')->translatedFormat('d F Y') }}</td>
                    <td>{{ $order->items_count ?? $order->items->count() }} menu</td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></td>
                    <td><span class="badge badge-{{ $order->payment_status }}">{{ $order->payment_status_label }}</span></td>
                    <td>
                        <a href="{{ route($showRoute, $order) }}" class="button button-secondary button-small">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div style="padding: 28px 12px; text-align: center;">
                            <strong>Tidak ada pesanan ditemukan.</strong>
                            <p style="color: var(--color-muted); margin-top: 6px;">
                                Sesuaikan kata kunci atau filter pencarian.
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

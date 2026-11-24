{{-- Detail pesanan (read-only). Variabel: $order dengan relasi user dan items sudah dimuat. --}}
<div class="detail-grid">
    <section class="panel">
        <div class="panel-header">
            <div><h2>Pelanggan &amp; Acara</h2></div>
        </div>
        <div class="panel-body">
            <dl class="detail-list">
                <dt>Pelanggan</dt>
                <dd>{{ $order->user?->name ?? '-' }}</dd>
                <dt>WhatsApp</dt>
                <dd>{{ $order->customer_phone }}</dd>
                <dt>Tanggal acara</dt>
                <dd>{{ $order->event_date->locale('id')->translatedFormat('l, d F Y') }}</dd>
                <dt>Alamat acara</dt>
                <dd>{{ $order->event_address }}</dd>
                <dt>Catatan</dt>
                <dd>{{ $order->notes ?: '—' }}</dd>
                <dt>Dibuat</dt>
                <dd>{{ $order->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</dd>
            </dl>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div><h2>Status</h2></div>
        </div>
        <div class="panel-body">
            <dl class="detail-list">
                <dt>Status pesanan</dt>
                <dd><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></dd>
                <dt>Status pembayaran</dt>
                <dd><span class="badge badge-{{ $order->payment_status }}">{{ $order->payment_status_label }}</span></dd>
            </dl>
        </div>
    </section>
</div>

<section class="panel" style="margin-top: 20px;">
    <div class="panel-header">
        <div><h2>Menu Dipesan</h2></div>
        <span class="badge badge-active">{{ $order->items->sum('quantity') }} porsi</span>
    </div>
    <div class="panel-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Porsi</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->items as $item)
                        <tr>
                            <td>{{ $item->menu_name }}</td>
                            <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--color-muted);">
                                Tidak ada item pada pesanan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="summary-list">
            <div><span>Total</span><span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
            <div><span>DP</span><span>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span></div>
            <div><span>Sisa pembayaran</span><span>Rp {{ number_format($order->total_amount - $order->dp_amount, 0, ',', '.') }}</span></div>
        </div>
    </div>
</section>

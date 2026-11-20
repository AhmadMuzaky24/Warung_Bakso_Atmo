@extends('layouts.public')

@section('title', 'Detail Pesanan ' . $order->booking_code . ' - ' . config('bakso_atmo.name'))
@section('hide_footer_cta', true)

@section('content')
    @once
        @push('styles')
            @include('customer.partials.styles')
        @endpush
    @endonce

    @php
        $statusBadge = [
            'submitted' => ['customer-badge--submitted', 'Diajukan'],
            'processing' => ['customer-badge--processing', 'Diproses'],
            'completed' => ['customer-badge--completed', 'Selesai'],
            'rejected' => ['customer-badge--rejected', 'Ditolak'],
        ];
        $paymentBadge = [
            'pending' => ['customer-badge--pending', 'DP belum dibayar'],
            'verified' => ['customer-badge--verified', 'DP terverifikasi'],
            'rejected' => ['customer-badge--payment-rejected', 'Bukti DP ditolak'],
        ];
        $badge = $statusBadge[$order->status] ?? ['customer-badge--processing', ucfirst($order->status)];
        $pay = $paymentBadge[$order->payment_status] ?? ['customer-badge--pending', ucfirst($order->payment_status)];
        $dpPercent = (int) config('bakso_atmo.booking.dp_percent', 50);
        $totalPortions = $order->items->sum('quantity');
    @endphp

    <div class="customer-shell">
        <a href="{{ route('customer.catering.index') }}" class="back-link">&larr; Kembali ke riwayat</a>

        @if (session('success'))
            <div class="customer-alert customer-alert--success" role="status" aria-live="polite">
                {{ session('success') }}
            </div>
        @endif

        @if ($order->status === 'rejected')
            <div class="customer-alert customer-alert--error" role="alert">
                @if ($whatsappUrl)
                    Pesanan ditolak. Hubungi admin lewat WhatsApp untuk penjelasan.
                @else
                    Pesanan ditolak. Hubungi cabang untuk penjelasan.
                @endif
            </div>
        @endif

        <div class="customer-page-head">
            <div>
                <h1>{{ $order->booking_code }}</h1>
                <p>Dibuat {{ $order->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <div>
                <span class="customer-badge {{ $badge[0] }}">{{ $badge[1] }}</span>
                <span class="customer-badge {{ $pay[0] }}" style="margin-left:6px">{{ $pay[1] }}</span>
            </div>
        </div>

        @if ($order->payment_status === 'pending')
            <div class="customer-alert">
                DP Rp {{ number_format($order->dp_amount, 0, ',', '.') }} belum dibayar.
                @if ($whatsappUrl)
                    Konfirmasi pembayaran lewat WhatsApp.
                @else
                    Konfirmasi pembayaran ke cabang.
                @endif
            </div>
        @endif

        <div class="customer-card">
            <h2>Detail acara</h2>
            <div class="customer-total customer-detail" style="border-top:0;padding-top:0;margin-top:0">
                <div><span>Tanggal</span><span>{{ $order->event_date->locale('id')->translatedFormat('d F Y') }}</span></div>
                <div><span>Alamat</span><span>{{ $order->event_address }}</span></div>
                <div><span>WhatsApp</span><span>{{ $order->customer_phone }}</span></div>
                <div><span>Catatan</span><span>{{ $order->notes ?: '—' }}</span></div>
            </div>
        </div>

        <div class="customer-card">
            <h2>Menu</h2>

            @if ($order->items->isEmpty())
                <p>Tidak ada item pada pesanan ini.</p>
            @else
                <table class="customer-table">
                    <caption style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)">Item pesanan katering</caption>
                    <thead>
                        <tr>
                            <th scope="col">Menu</th>
                            <th scope="col">Harga &times; porsi</th>
                            <th scope="col">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td data-label="Menu">{{ $item->menu_name }}</td>
                                <td data-label="Harga &times; porsi">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }} &times; {{ $item->quantity }}
                                </td>
                                <td data-label="Subtotal" data-num>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="customer-total">
                    <div><span>Subtotal ({{ $totalPortions }} porsi)</span><span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
                    <div><span>DP ({{ $dpPercent }}%)</span><span>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span></div>
                    <div><span>Sisa bayar</span><span>Rp {{ number_format($order->total_amount - $order->dp_amount, 0, ',', '.') }}</span></div>
                </div>
            @endif
        </div>

        <div class="customer-card">
            @if ($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn btn--dark">Konfirmasi via WhatsApp</a>
                <p style="margin-top:12px;font-size:14px;color:#5f4d3c;text-align:center;max-width:340px;margin-inline:auto">
                    Kirim kode booking &amp; ringkasan ke admin lewat WhatsApp.
                </p>
            @else
                <p style="font-size:14px;color:#5f4d3c;text-align:center;max-width:340px;margin-inline:auto">Nomor WhatsApp belum dikonfigurasi</p>
            @endif

            <p style="margin-top:16px">
                <a href="{{ route('customer.dashboard') }}" class="btn btn--light">Cek Status</a>
            </p>
        </div>
    </div>
@endsection

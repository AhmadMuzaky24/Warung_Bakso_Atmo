@extends('layouts.public')

@section('title', 'Riwayat Pesanan Katering - ' . config('bakso_atmo.name'))

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
    @endphp

    <div class="customer-shell">
        <div class="customer-page-head">
            <div>
                <h1>Riwayat Pesanan</h1>
                <p>Semua pesanan katering milik kamu.</p>
            </div>
            <a href="{{ route('customer.catering.create') }}" class="btn btn--dark">Pesan Katering</a>
        </div>

        @if (session('success'))
            <div class="customer-alert customer-alert--success" role="status" aria-live="polite">
                {{ session('success') }}
            </div>
        @endif

        @if ($orders->isEmpty())
            <div class="customer-empty">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#c0440f" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 11h18l-2 8H5l-2-8Z" />
                    <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                </svg>
                <h3>Belum ada pesanan katering</h3>
                <p>Pesanan yang kamu kirim akan muncul di sini.</p>
                <a href="{{ route('customer.catering.create') }}" class="btn btn--dark">Pesan Katering</a>
                @if (request()->integer('page') > 1)
                    <p style="margin-top:16px">
                        <a href="{{ route('customer.catering.index') }}" class="back-link">Kembali ke halaman 1</a>
                    </p>
                @endif
            </div>
        @else
            <div class="customer-card">
                <table class="customer-table">
                    <caption style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)">Riwayat pesanan katering</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Tanggal acara</th>
                            <th scope="col">Menu</th>
                            <th scope="col">Total</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php($badge = $statusBadge[$order->status] ?? ['customer-badge--processing', ucfirst($order->status)])
                            @php($pay = $paymentBadge[$order->payment_status] ?? ['customer-badge--pending', ucfirst($order->payment_status)])
                            <tr>
                                <td data-label="Kode">
                                    <a href="{{ route('customer.catering.show', $order) }}" style="font-weight:600;letter-spacing:.02em">
                                        {{ $order->booking_code }}
                                    </a>
                                </td>
                                <td data-label="Tanggal acara">{{ $order->event_date->locale('id')->translatedFormat('d F Y') }}</td>
                                <td data-label="Menu">{{ $order->items_count ?? '-' }} item</td>
                                <td data-label="Total" data-num>
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    <small style="display:block;color:#5f4d3c">DP Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</small>
                                </td>
                                <td data-label="Status">
                                    <span class="customer-badge {{ $badge[0] }}">{{ $badge[1] }}</span>
                                    <span class="customer-badge {{ $pay[0] }}" style="margin-top:6px">{{ $pay[1] }}</span>
                                </td>
                                <td data-label="Aksi">
                                    <a href="{{ route('customer.catering.show', $order) }}" class="btn btn--light">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                {{ $orders->onEachSide(1)->links('pagination::simple-default') }}
            @endif
        @endif
    </div>
@endsection

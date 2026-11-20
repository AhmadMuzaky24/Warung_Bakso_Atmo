@extends('layouts.public')

@section('title', 'Dashboard Customer - ' . config('bakso_atmo.name'))

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
    @endphp

    <div class="customer-shell">
        <div class="customer-page-head">
            <div>
                <h1>Halo, {{ auth()->user()->name }}</h1>
                <p>Kelola pesanan katering kamu di sini.</p>
            </div>
            <a href="{{ route('customer.catering.create') }}" class="btn btn--dark">Pesan Katering</a>
        </div>

        <div class="grid grid--3">
            <div class="customer-card">
                <strong>{{ $totalOrders }}</strong>
                <small>Total pesanan</small>
            </div>
            <div class="customer-card">
                <strong>{{ $submittedOrders }}</strong>
                <small>Sedang diproses</small>
            </div>
            <div class="customer-card">
                <strong>{{ $completedOrders }}</strong>
                <small>Selesai</small>
            </div>
        </div>

        <h2 class="section__title section__title--spaced" style="font-size:20px">Pesanan terakhir</h2>

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
            </div>
        @else
            <div class="grid grid--3">
                @foreach ($orders->take(3) as $order)
                    @php($badge = $statusBadge[$order->status] ?? ['customer-badge--processing', ucfirst($order->status)])
                    <div class="customer-card">
                        <a href="{{ route('customer.catering.show', $order) }}">
                            <strong style="font-size:13px;letter-spacing:.02em">{{ $order->booking_code }}</strong>
                            <span class="customer-badge {{ $badge[0] }}">{{ $badge[1] }}</span>
                            <p>{{ $order->event_date->locale('id')->translatedFormat('d F Y') }}</p>
                            <p>Rp {{ number_format($order->total_amount, 0, ',', '.') }} · DP Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</p>
                        </a>
                    </div>
                @endforeach
            </div>

            <p style="margin-top:16px">
                <a href="{{ route('customer.catering.index') }}" class="back-link"
                   style="display:inline-flex;align-items:center;min-height:44px">Lihat semua pesanan &rarr;</a>
            </p>
        @endif
    </div>
@endsection

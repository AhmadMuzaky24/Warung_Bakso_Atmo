@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('eyebrow', 'RINGKASAN ADMIN')
@section('page-title', 'Dashboard Admin')
@section('page-description', 'Pantau ringkasan pesanan katering, verifikasi pembayaran, dan data master Bakso Atmo.')

@section('page-actions')
    <div class="page-actions-group">
        <a href="{{ route('admin.catering.index') }}" class="button button-secondary">Verifikasi Pembayaran</a>
        <a href="{{ route('admin.reports.index') }}" class="button button-primary">Laporan &amp; Ekspor XLSX</a>
    </div>
@endsection

@section('content')
    <section class="stats-grid">
        <article class="info-card stat-card--accent">
            <small>Total Pesanan Katering</small>
            <strong>{{ $totalOrders }}</strong>
            <span class="stat-sub">Diajukan: {{ $submittedOrders }} · Diproses: {{ $processingOrders }} · Selesai: {{ $completedOrders }}</span>
        </article>

        <article class="info-card">
            <small>Menunggu Verifikasi Pembayaran</small>
            <strong>{{ $pendingPayments }}</strong>
            <span class="stat-sub">Potensi DP: Rp {{ number_format($pendingDp, 0, ',', '.') }}</span>
        </article>

        <article class="info-card">
            <small>DP Terverifikasi</small>
            <strong>Rp {{ number_format($verifiedDp, 0, ',', '.') }}</strong>
            <span class="stat-sub">{{ $verifiedPayments }} pesanan terverifikasi · Ditolak: {{ $rejectedPayments }}</span>
        </article>

        <article class="info-card">
            <small>Nilai Pesanan Aktif</small>
            <strong>Rp {{ number_format($activeRevenue, 0, ',', '.') }}</strong>
            <span class="stat-sub">Selesai: Rp {{ number_format($completedRevenue, 0, ',', '.') }}</span>
        </article>

        <article class="info-card">
            <small>Acara Hari Ini / 7 Hari Ke Depan</small>
            <strong>{{ $todayEvents }} / {{ $upcomingEvents }}</strong>
            <span class="stat-sub">Pesanan aktif (diajukan &amp; diproses) · Ditolak: {{ $rejectedOrders }}</span>
        </article>

        <article class="info-card">
            <small>Data Master Sistem</small>
            <strong>{{ $availableMenusCount }} / {{ $totalMenusCount }} Menu</strong>
            <span class="stat-sub">{{ $categoriesCount }} kategori · {{ $activeUsersCount }} pengguna aktif</span>
        </article>
    </section>

    <section class="panel" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Pembayaran Menunggu Verifikasi</h2>
                <p>Lima pesanan terbaru dengan status pembayaran menunggu verifikasi.</p>
            </div>
            <a href="{{ route('admin.catering.index', ['payment_status' => 'pending']) }}" class="button button-secondary button-small">
                Lihat Semua
            </a>
        </div>

        <div class="panel-body">
            @include('catering.partials.orders-table', [
                'orders' => $pendingPaymentOrders,
                'showRoute' => 'admin.catering.show',
            ])
        </div>
    </section>

    <section class="panel" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Pesanan Katering Terbaru</h2>
                <p>Lima pesanan terakhir yang masuk ke sistem.</p>
            </div>
            <a href="{{ route('admin.catering.index') }}" class="button button-secondary button-small">
                Daftar Pesanan
            </a>
        </div>

        <div class="panel-body">
            @include('catering.partials.orders-table', [
                'orders' => $recentOrders,
                'showRoute' => 'admin.catering.show',
            ])
        </div>
    </section>
@endsection

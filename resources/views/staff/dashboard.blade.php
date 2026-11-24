@extends('layouts.admin')

@section('title', 'Dashboard Staff')
@section('eyebrow', 'OPERASIONAL')
@section('page-title', 'Dashboard Staff')
@section('page-description', 'Ringkasan pesanan katering yang perlu ditindaklanjuti.')

@section('page-actions')
    <a href="{{ route('staff.catering.index') }}" class="button button-primary">Lihat Semua Pesanan</a>
@endsection

@section('content')
    <section class="info-grid" style="margin-top: 0;">
        <article class="info-card">
            <small>Total Pesanan</small>
            <strong>{{ $totalOrders }}</strong>
        </article>

        <article class="info-card">
            <small>Menunggu Diproses (Diajukan)</small>
            <strong>{{ $submittedOrders }}</strong>
        </article>

        <article class="info-card">
            <small>Sedang Diproses</small>
            <strong>{{ $processingOrders }}</strong>
        </article>

        <article class="info-card">
            <small>Selesai</small>
            <strong>{{ $completedOrders }}</strong>
        </article>

        <article class="info-card">
            <small>Ditolak</small>
            <strong>{{ $rejectedOrders }}</strong>
        </article>

        <article class="info-card">
            <small>Acara Hari Ini (belum selesai)</small>
            <strong>{{ $todayEvents }}</strong>
        </article>
    </section>

    <section class="panel" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Pesanan Terbaru</h2>
                <p>Lima pesanan terakhir yang masuk.</p>
            </div>
        </div>

        <div class="panel-body">
            @include('catering.partials.orders-table', [
                'orders' => $recentOrders,
                'showRoute' => 'staff.catering.show',
            ])
        </div>
    </section>
@endsection

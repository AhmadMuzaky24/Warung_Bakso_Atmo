@extends('layouts.admin')

@section('title', 'Laporan Katering')
@section('eyebrow', 'LAPORAN & EKSPOR')
@section('page-title', 'Laporan Pesanan Katering')
@section('page-description', 'Ringkasan transaksi katering berdasarkan rentang tanggal acara serta ekspor ke XLSX.')

@section('page-actions')
    <a href="{{ route('admin.reports.export', $filters) }}" class="button button-primary">
        Ekspor XLSX ({{ $summary['total_orders'] }})
    </a>
@endsection

@section('content')
    <section class="stats-grid">
        <article class="info-card stat-card--accent">
            <small>Pesanan Sesuai Filter</small>
            <strong>{{ $summary['total_orders'] }}</strong>
            <span class="stat-sub">Selesai: {{ $summary['completed_orders'] }} · Bayar terverifikasi: {{ $summary['verified_orders'] }}</span>
        </article>

        <article class="info-card">
            <small>Total Nilai Pesanan</small>
            <strong>Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</strong>
            <span class="stat-sub">Akumulasi total pesanan periode terpilih</span>
        </article>

        <article class="info-card">
            <small>Total DP Tercatat / Terverifikasi</small>
            <strong>Rp {{ number_format($summary['verified_dp'], 0, ',', '.') }}</strong>
            <span class="stat-sub">Total DP seluruh filter: Rp {{ number_format($summary['total_dp'], 0, ',', '.') }}</span>
        </article>

        <article class="info-card">
            <small>Sisa Pembayaran</small>
            <strong>Rp {{ number_format($summary['remaining_balance'], 0, ',', '.') }}</strong>
            <span class="stat-sub">Selisih total pesanan dikurangi DP</span>
        </article>
    </section>

    <section class="panel" style="margin-top: 24px;">
        <div class="panel-header">
            <div>
                <h2>Data Laporan</h2>
                <p>Saring berdasarkan rentang tanggal acara, status pesanan, status pembayaran, atau kata kunci.</p>
            </div>
            <span class="badge badge-active">{{ $orders->total() }} Data</span>
        </div>

        <div class="panel-body">
            @include('catering.partials.filters', [
                'action' => route('admin.reports.index'),
                'filters' => $filters,
            ])

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Pelanggan</th>
                            <th>Tanggal Acara</th>
                            <th>Menu Dipesan</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">DP</th>
                            <th class="text-right">Sisa Bayar</th>
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
                                <td>
                                    <div class="table-primary">{{ $order->items->sum('quantity') }} porsi ({{ $order->items_count }} menu)</div>
                                    <div class="table-secondary">
                                        {{ $order->items->pluck('menu_name')->take(2)->implode(', ') }}{{ $order->items->count() > 2 ? ', ...' : '' }}
                                    </div>
                                </td>
                                <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format(max(0, $order->total_amount - $order->dp_amount), 0, ',', '.') }}</td>
                                <td><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></td>
                                <td><span class="badge badge-{{ $order->payment_status }}">{{ $order->payment_status_label }}</span></td>
                                <td>
                                    <a href="{{ route('admin.catering.show', $order) }}" class="button button-secondary button-small">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div style="padding: 28px 12px; text-align: center;">
                                        <strong>Tidak ada data laporan pada filter ini.</strong>
                                        <p style="color: var(--color-muted); margin-top: 6px;">
                                            Ubah rentang tanggal atau reset filter untuk melihat pesanan lain.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="pagination">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

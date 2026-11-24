@extends('layouts.admin')

@section('title', 'Pesanan Katering')
@section('eyebrow', 'TRANSAKSI')
@section('page-title', 'Pesanan Katering')
@section('page-description', 'Seluruh pesanan katering dan verifikasi pembayaran DP.')

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Daftar Pesanan</h2>
                <p>Cari berdasarkan kode booking atau nama pelanggan, lalu saring menurut status dan tanggal acara.</p>
            </div>
            <span class="badge badge-active">{{ $orders->total() }} Pesanan</span>
        </div>

        <div class="panel-body">
            @include('catering.partials.filters', [
                'action' => route('admin.catering.index'),
                'filters' => $filters,
            ])

            @include('catering.partials.orders-table', [
                'orders' => $orders,
                'showRoute' => 'admin.catering.show',
            ])

            @if ($orders->hasPages())
                <div class="pagination">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

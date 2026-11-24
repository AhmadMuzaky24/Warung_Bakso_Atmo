@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->booking_code)
@section('eyebrow', 'TRANSAKSI')
@section('page-title', 'Pesanan ' . $order->booking_code)
@section('page-description', 'Detail pesanan dan verifikasi pembayaran.')

@section('page-actions')
    <a href="{{ route('admin.catering.index') }}" class="button button-secondary">&larr; Kembali ke Daftar</a>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <span>!</span>
            <div>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <div class="panel-stack">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Verifikasi Pembayaran</h2>
                    <p>Status pesanan dikelola Staff dan tidak dapat diubah dari halaman ini.</p>
                </div>
            </div>

            <div class="panel-body">
                <form method="POST" action="{{ route('admin.catering.update-payment-status', $order) }}" class="search-form" style="max-width: 460px;">
                    @csrf
                    @method('PATCH')

                    <select name="payment_status" class="form-control" aria-label="Status pembayaran" required>
                        @foreach (\App\Models\CateringOrder::PAYMENT_STATUS_LABELS as $key => $label)
                            <option value="{{ $key }}" @selected(old('payment_status', $order->payment_status) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="button button-primary">Simpan Pembayaran</button>
                </form>
            </div>
        </section>

        @include('catering.partials.order-detail', ['order' => $order])
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->booking_code)
@section('eyebrow', 'OPERASIONAL')
@section('page-title', 'Pesanan ' . $order->booking_code)
@section('page-description', 'Detail pesanan dan pembaruan status pengerjaan.')

@section('page-actions')
    <a href="{{ route('staff.catering.index') }}" class="button button-secondary">&larr; Kembali ke Daftar</a>
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
                    <h2>Ubah Status Pesanan</h2>
                    <p>Status pembayaran dikelola Admin dan tidak dapat diubah dari halaman ini.</p>
                </div>
            </div>

            <div class="panel-body">
                <form method="POST" action="{{ route('staff.catering.update-status', $order) }}" class="search-form" style="max-width: 460px;">
                    @csrf
                    @method('PATCH')

                    <select name="status" class="form-control" aria-label="Status pesanan" required>
                        @foreach (\App\Models\CateringOrder::STATUS_LABELS as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $order->status) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="button button-primary">Simpan Status</button>
                </form>
            </div>
        </section>

        @include('catering.partials.order-detail', ['order' => $order])
    </div>
@endsection

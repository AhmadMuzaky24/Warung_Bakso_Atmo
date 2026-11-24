{{-- Form pencarian + filter status + rentang tanggal acara. Dipakai Staff dan Admin.
     Variabel: $action (URL), $filters (array dari query string). --}}
@php
    $value = fn (string $key): string => is_string($filters[$key] ?? null) ? $filters[$key] : '';
    $hasFilter = collect(['search', 'status', 'payment_status', 'start_date', 'end_date'])
        ->contains(fn ($key) => $value($key) !== '');
@endphp

<form method="GET" action="{{ $action }}" class="filter-form">
    <div class="form-group form-group--search">
        <label for="search" class="form-label">Cari</label>
        <input type="text" id="search" name="search" class="form-control"
               placeholder="Kode booking / nama pelanggan" value="{{ $value('search') }}">
    </div>

    <div class="form-group">
        <label for="status" class="form-label">Status pesanan</label>
        <select id="status" name="status" class="form-control">
            <option value="">Semua</option>
            @foreach (\App\Models\CateringOrder::STATUS_LABELS as $key => $label)
                <option value="{{ $key }}" @selected($value('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="payment_status" class="form-label">Status pembayaran</label>
        <select id="payment_status" name="payment_status" class="form-control">
            <option value="">Semua</option>
            @foreach (\App\Models\CateringOrder::PAYMENT_STATUS_LABELS as $key => $label)
                <option value="{{ $key }}" @selected($value('payment_status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="start_date" class="form-label">Acara dari</label>
        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $value('start_date') }}">
    </div>

    <div class="form-group">
        <label for="end_date" class="form-label">Acara sampai</label>
        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $value('end_date') }}">
    </div>

    <button type="submit" class="button button-primary">Terapkan</button>

    @if ($hasFilter)
        <a href="{{ $action }}" class="button button-secondary">Reset</a>
    @endif
</form>

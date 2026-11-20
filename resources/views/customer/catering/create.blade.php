@extends('layouts.public')

@section('title', 'Pesan Katering - ' . config('bakso_atmo.name'))

@section('content')
    @once
        @push('styles')
            @include('customer.partials.styles')
        @endpush
    @endonce

    @php
        $dpPercent = (int) config('bakso_atmo.booking.dp_percent', 50);
        $earliestDate = \App\Support\BookingWindow::earliestEventDate();
        $minDate = $earliestDate->toDateString();
        $rows = old('items');
        if (! is_array($rows) || $rows === []) {
            $rows = [['menu_id' => '', 'quantity' => 1]];
        }
    @endphp

    <div class="customer-shell">
        <a href="{{ route('customer.dashboard') }}" class="back-link">&larr; Dashboard</a>

        <div class="customer-page-head">
            <div>
                <h1>Pesan Katering</h1>
                <p>Isi detail acara, kami konfirmasi setelah pesanan masuk.</p>
            </div>
        </div>

        <div class="customer-alert customer-alert--error" role="alert" id="form-alert"@if (! $errors->any()) hidden @endif>
            @if ($errors->any())
                @php
                    $fieldLabels = [
                        'event_date' => 'Tanggal acara',
                        'event_address' => 'Alamat pengiriman',
                        'customer_phone' => 'Nomor WhatsApp',
                        'notes' => 'Catatan',
                    ];
                    $labels = [];
                    foreach ($errors->keys() as $key) {
                        $labels[] = str_starts_with($key, 'items')
                            ? 'Menu'
                            : ($fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)));
                    }
                    $labels = array_values(array_unique($labels));
                @endphp
                Ada {{ count($labels) }} isian yang perlu diperbaiki: {{ implode(', ', $labels) }}.
            @endif
        </div>

        @if ($menus->isEmpty())
            <div class="customer-empty">
                <h3>Belum ada menu tersedia untuk katering</h3>
                <p>Menu sedang tidak tersedia. Silakan hubungi cabang untuk pemesanan katering.</p>
                <a href="{{ route('public.home') }}" class="btn btn--dark">Lihat halaman utama</a>
            </div>
        @else
            <form method="POST" action="{{ route('customer.catering.store') }}" class="customer-form"
                  id="booking-form" data-dp-percent="{{ $dpPercent }}" novalidate>
                @csrf

                <div class="customer-card">
                    <h2>1. Kontak</h2>

                    <div class="customer-form__row">
                        <label for="customer-name">Nama</label>
                        <input type="text" id="customer-name" value="{{ auth()->user()->name }}" readonly
                               aria-describedby="customer-name-hint">
                        <small id="customer-name-hint">Dari akun kamu.</small>
                    </div>

                    <div class="customer-form__row">
                        <label for="customer_phone">Nomor WhatsApp</label>
                        <input type="tel" name="customer_phone" id="customer_phone"
                               value="{{ old('customer_phone') }}"
                               inputmode="tel" autocomplete="tel" required
                               pattern="^(\+62|62|0)8[1-9][0-9]{6,11}$"
                               aria-describedby="customer_phone-hint{{ $errors->has('customer_phone') ? ' error-customer_phone' : '' }}"
                               aria-invalid="{{ $errors->has('customer_phone') ? 'true' : 'false' }}">
                        <small id="customer_phone-hint">Contoh: 081234567890 atau +6281234567890</small>
                        @error('customer_phone')
                            <small data-error id="error-customer_phone">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="customer-card">
                    <h2>2. Detail acara</h2>

                    <div class="customer-form__row">
                        <label for="event_date">Tanggal acara</label>
                        <input type="date" name="event_date" id="event_date" value="{{ old('event_date') }}"
                               min="{{ $minDate }}" required
                               aria-describedby="event_date-hint{{ $errors->has('event_date') ? ' error-event_date' : '' }}"
                               aria-invalid="{{ $errors->has('event_date') ? 'true' : 'false' }}">
                        <small id="event_date-hint">Paling cepat {{ $earliestDate->translatedFormat('j F Y') }}. Pemesanan H-1 hanya sampai pukul {{ \App\Support\BookingWindow::cutoffTime() }} WIB.</small>
                        @error('event_date')
                            <small data-error id="error-event_date">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="customer-form__row">
                        <label for="event_address">Alamat pengiriman</label>
                        <textarea name="event_address" id="event_address" rows="3" maxlength="255" required
                                  aria-invalid="{{ $errors->has('event_address') ? 'true' : 'false' }}"
                                  aria-describedby="{{ $errors->has('event_address') ? 'error-event_address' : '' }}">{{ old('event_address') }}</textarea>
                        @error('event_address')
                            <small data-error id="error-event_address">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="customer-form__row">
                        <label for="notes">Catatan (opsional)</label>
                        <textarea name="notes" id="notes" rows="3" maxlength="1000"
                                  aria-invalid="{{ $errors->has('notes') ? 'true' : 'false' }}"
                                  aria-describedby="notes-hint{{ $errors->has('notes') ? ' error-notes' : '' }}">{{ old('notes') }}</textarea>
                        <small id="notes-hint">Contoh: tidak pedas, tambah 10 porsi.</small>
                        @error('notes')
                            <small data-error id="error-notes">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="customer-card">
                    <h2>3. Menu</h2>

                    @error('items')
                        <small data-error id="error-items" style="display:block;margin-bottom:12px">{{ $message }}</small>
                    @enderror

                    <div id="item-rows" class="customer-form">
                        @foreach ($rows as $i => $row)
                            <div class="customer-form__row" data-item-row>
                                <label for="items-{{ $i }}-menu_id">Menu {{ $i + 1 }}</label>
                                <select name="items[{{ $i }}][menu_id]" id="items-{{ $i }}-menu_id" required
                                        data-menu-select
                                        aria-invalid="{{ $errors->has('items.'.$i.'.menu_id') ? 'true' : 'false' }}">
                                    <option value="">Pilih menu</option>
                                    @foreach ($menus as $menu)
                                        <option value="{{ $menu->id }}" data-price="{{ $menu->price }}"
                                            @selected((string) ($row['menu_id'] ?? '') === (string) $menu->id)>
                                            {{ $menu->name }} — Rp {{ number_format($menu->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('items.'.$i.'.menu_id')
                                    <small data-error>{{ $message }}</small>
                                @enderror

                                <label for="items-{{ $i }}-quantity">Jumlah porsi</label>
                                <input type="number" name="items[{{ $i }}][quantity]" id="items-{{ $i }}-quantity"
                                       value="{{ (int) ($row['quantity'] ?? 1) }}" min="1" max="500" required
                                       inputmode="numeric" data-qty
                                       aria-label="Jumlah porsi menu {{ $i + 1 }}"
                                       aria-invalid="{{ $errors->has('items.'.$i.'.quantity') ? 'true' : 'false' }}">
                                @error('items.'.$i.'.quantity')
                                    <small data-error>{{ $message }}</small>
                                @enderror

                                <small data-row-subtotal>—</small>

                                @if ($i > 0)
                                    <button type="button" class="btn btn--light" data-remove-row>Hapus menu ini</button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <p style="margin-top:16px">
                        <button type="button" class="btn btn--light" id="add-row">+ Tambah menu</button>
                    </p>
                </div>

                <div class="customer-card">
                    <h2>Ringkasan</h2>
                    <div class="customer-total">
                        <div><span>Subtotal</span><span id="sum-subtotal">—</span></div>
                        <div><span>DP ({{ $dpPercent }}%)</span><span id="sum-dp">—</span></div>
                        <div><span>Sisa bayar</span><span id="sum-rest">—</span></div>
                    </div>
                    <p style="margin:16px 0 0;font-size:14px;color:#5f4d3c">
                        Estimasi dihitung ulang oleh server saat booking dikirim.
                    </p>
                    <p style="margin-top:16px">
                        <button type="submit" class="btn btn--dark" id="submit-btn">Kirim Booking</button>
                    </p>
                </div>
            </form>

            <template id="item-template">
                <div class="customer-form__row" data-item-row>
                    <label>Menu</label>
                    <select data-menu-select required>
                        <option value="">Pilih menu</option>
                        @foreach ($menus as $menu)
                            <option value="{{ $menu->id }}" data-price="{{ $menu->price }}">
                                {{ $menu->name }} — Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <label>Jumlah porsi</label>
                    <input type="number" value="1" min="1" max="500" required inputmode="numeric" data-qty
                           aria-label="Jumlah porsi">
                    <small data-row-subtotal>—</small>
                    <button type="button" class="btn btn--light" data-remove-row>Hapus menu ini</button>
                </div>
            </template>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('booking-form');
            if (!form) { return; }

            var rowsWrap = document.getElementById('item-rows');
            var template = document.getElementById('item-template');
            var dpPercent = parseInt(form.getAttribute('data-dp-percent'), 10) || 0;

            function rupiah(n) {
                return 'Rp ' + n.toLocaleString('id-ID');
            }

            function recalc() {
                var total = 0;
                var portions = 0;
                var rows = rowsWrap.querySelectorAll('[data-item-row]');

                rows.forEach(function (row) {
                    var select = row.querySelector('[data-menu-select]');
                    var qtyInput = row.querySelector('[data-qty]');
                    var label = row.querySelector('[data-row-subtotal]');
                    var opt = select.options[select.selectedIndex];
                    var price = opt ? parseInt(opt.getAttribute('data-price'), 10) || 0 : 0;
                    var qty = parseInt(qtyInput.value, 10) || 0;
                    var line = price * qty;

                    if (price > 0 && qty > 0) {
                        total += line;
                        portions += qty;
                        label.textContent = rupiah(price) + ' × ' + qty + ' = ' + rupiah(line);
                    } else {
                        label.textContent = '—';
                    }
                });

                var dp = Math.round(total * dpPercent / 100);
                document.getElementById('sum-subtotal').textContent = total > 0 ? rupiah(total) : '—';
                document.getElementById('sum-dp').textContent = total > 0 ? rupiah(dp) : '—';
                document.getElementById('sum-rest').textContent = total > 0 ? rupiah(total - dp) : '—';
            }

            function reindex() {
                rowsWrap.querySelectorAll('[data-item-row]').forEach(function (row, i) {
                    var select = row.querySelector('[data-menu-select]');
                    var qty = row.querySelector('[data-qty]');
                    select.name = 'items[' + i + '][menu_id]';
                    select.id = 'items-' + i + '-menu_id';
                    qty.name = 'items[' + i + '][quantity]';
                    qty.id = 'items-' + i + '-quantity';

                    var labels = row.querySelectorAll('label');
                    if (labels[0]) {
                        labels[0].setAttribute('for', select.id);
                        labels[0].textContent = 'Menu ' + (i + 1);
                    }
                    if (labels[1]) {
                        labels[1].setAttribute('for', qty.id);
                        labels[1].textContent = 'Jumlah porsi ' + (i + 1);
                    }

                    row.querySelectorAll('small[data-js-error]').forEach(function (box) {
                        var kind = box.getAttribute('data-for');
                        if (!kind) { return; }
                        box.id = 'error-items-' + i + '-' + kind;
                        (kind === 'menu_id' ? select : qty).setAttribute('aria-describedby', box.id);
                    });
                });
            }

            rowsWrap.addEventListener('input', recalc);
            rowsWrap.addEventListener('change', recalc);

            document.getElementById('add-row').addEventListener('click', function () {
                var clone = template.content.cloneNode(true);
                rowsWrap.appendChild(clone);
                reindex();
                recalc();
            });

            rowsWrap.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-remove-row]');
                if (!btn) { return; }
                if (rowsWrap.querySelectorAll('[data-item-row]').length < 2) { return; }
                btn.closest('[data-item-row]').remove();
                reindex();
                recalc();
            });

            var alertBox = document.getElementById('form-alert');

            function itemKind(el) {
                if (!el.name) { return null; }
                if (el.name.indexOf('[menu_id]') !== -1) { return 'menu_id'; }
                if (el.name.indexOf('[quantity]') !== -1) { return 'quantity'; }
                return null;
            }

            function itemIndex(el) {
                var match = el.name ? el.name.match(/items\[(\d+)\]/) : null;
                return match ? parseInt(match[1], 10) : 0;
            }

            function labelFor(el) {
                var kind = itemKind(el);
                if (kind === 'menu_id') { return 'Menu ' + (itemIndex(el) + 1); }
                if (kind === 'quantity') { return 'Jumlah porsi ' + (itemIndex(el) + 1); }
                if (el.id) {
                    var label = document.querySelector('label[for="' + el.id + '"]');
                    if (label) { return label.textContent.trim(); }
                }
                return el.getAttribute('aria-label') || 'Isian';
            }

            // 4 varian teks Indonesia (dikunci ui-spec §4.4.1) — teks sendiri, bukan bawaan browser
            function messageFor(field) {
                if (field.validity.valueMissing) {
                    return field.tagName === 'SELECT' ? 'Pilih menu dulu.' : 'Bagian ini wajib diisi.';
                }
                if (field.validity.patternMismatch) { return 'Format belum benar.'; }
                if (field.type === 'date') { return 'Tanggal acara belum benar.'; }
                return 'Format belum benar.';
            }

            function hasServerError(field) {
                var next = field.nextElementSibling;
                if (next && next.matches('small[data-error]:not([data-js-error])')) { return true; }

                // pesan server bisa tidak persis setelah field (mis. ada <small> hint di antaranya),
                // jadi cek juga id yang ditunjuk aria-describedby
                var described = (field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
                return described.some(function (id) {
                    var el = document.getElementById(id);
                    return !!(el && el.matches('small[data-error]:not([data-js-error])'));
                });
            }

            function showInlineError(field) {
                field.setAttribute('aria-invalid', 'true');

                // pesan validasi dari server (blade error) tidak ditimpa, tidak diduplikasi
                if (hasServerError(field)) { return; }

                var kind = itemKind(field);
                var next = field.nextElementSibling;
                var box = next && next.matches('small[data-js-error]') ? next : null;

                if (!box) {
                    box = document.createElement('small');
                    box.setAttribute('data-js-error', '');
                    box.setAttribute('data-error', '');
                    if (kind) { box.setAttribute('data-for', kind); }
                    box.id = 'error-' + (field.id || field.name || 'field');
                    field.insertAdjacentElement('afterend', box);
                }

                box.textContent = messageFor(field);
                field.setAttribute('aria-describedby', box.id);
            }

            function clearInlineError(field) {
                var next = field.nextElementSibling;
                if (next && next.matches('small[data-js-error]')) {
                    if (field.getAttribute('aria-describedby') === next.id) {
                        field.removeAttribute('aria-describedby');
                    }
                    next.remove();
                }
                field.setAttribute('aria-invalid', 'false');
            }

            // cleanup per-field (bukan per-form) lewat delegasi, karena baris menu dinamis
            function cleanupField(e) {
                var field = e.target;
                if (!field || !field.matches || !field.matches('input, select, textarea')) { return; }
                if (field.checkValidity()) { clearInlineError(field); }
            }

            form.addEventListener('input', cleanupField);
            form.addEventListener('change', cleanupField);

            form.addEventListener('submit', function (e) {
                var submit = document.getElementById('submit-btn');

                // reset tiap submit
                if (alertBox) {
                    alertBox.hidden = true;
                    alertBox.textContent = '';
                }
                form.querySelectorAll('small[data-js-error]').forEach(function (box) {
                    var prev = box.previousElementSibling;
                    if (prev && prev.getAttribute && prev.getAttribute('aria-describedby') === box.id) {
                        prev.removeAttribute('aria-describedby');
                    }
                    box.remove();
                });
                form.querySelectorAll('[aria-invalid]').forEach(function (el) {
                    el.setAttribute('aria-invalid', 'false');
                });

                if (!form.checkValidity()) {
                    e.preventDefault();

                    var invalid = Array.prototype.slice.call(form.querySelectorAll(':invalid'));
                    var labels = [];
                    invalid.forEach(function (el) {
                        showInlineError(el);
                        var label = labelFor(el);
                        if (labels.indexOf(label) === -1) { labels.push(label); }
                    });

                    if (alertBox) {
                        alertBox.hidden = false;
                        alertBox.textContent = 'Ada ' + labels.length + ' isian yang perlu diperbaiki: ' + labels.join(', ') + '.';
                    }

                    if (invalid[0]) {
                        invalid[0].focus({ preventScroll: true });
                        // 'instant' eksplisit: public.css:306 bikin scroll halaman beranimasi,
                        // tanpa ini scrollIntoView ikut animasi dan tidak jalan di tab tidak aktif
                        invalid[0].scrollIntoView({ block: 'center', behavior: 'instant' });
                    }

                    return; // jangan disable tombol kalau belum valid
                }

                submit.disabled = true;
                submit.setAttribute('aria-busy', 'true');
                submit.textContent = 'Mengirim…';
            });

            recalc();
        })();
    </script>
@endpush

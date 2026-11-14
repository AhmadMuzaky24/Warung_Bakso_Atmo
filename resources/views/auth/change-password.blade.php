@extends('layouts.admin')

@section('title', 'Ubah Password')
@section('eyebrow', 'PENGATURAN AKUN')
@section('page-title', 'Ubah Password')
@section('page-description', 'Perbarui password untuk menjaga keamanan akun Anda.')

@section('content')
    <div class="panel" style="max-width: 760px;">
        <div class="panel-header">
            <div>
                <h2>Keamanan Akun</h2>
                <p>Masukkan password saat ini sebelum menentukan password baru.</p>
            </div>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="current_password" class="form-label">
                            Password Saat Ini
                        </label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control"
                            placeholder="Masukkan password saat ini"
                            autocomplete="current-password"
                            required
                        >
                        @error('current_password')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            Password Baru
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            required
                        >
                        @error('password')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            Konfirmasi Password Baru
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi password baru"
                            autocomplete="new-password"
                            required
                        >
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('dashboard') }}" class="button button-secondary">
                        Batal
                    </a>

                    <button type="submit" class="button button-primary">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
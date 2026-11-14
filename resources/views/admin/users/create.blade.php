@extends('layouts.admin')

@section('title', 'Tambah Pengguna')
@section('eyebrow', 'MANAJEMEN PENGGUNA')
@section('page-title', 'Tambah Pengguna')
@section('page-description', 'Tambahkan akun baru ke dalam sistem Bakso Atmo.')

@section('page-actions')
    <a href="{{ route('admin.users.index') }}" class="button button-secondary">
        Kembali
    </a>
@endsection

@section('content')
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2>Informasi Akun</h2>
                <p>Lengkapi informasi untuk membuat akun pengguna baru.</p>
            </div>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap"
                            required
                        >
                        @error('name')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            required
                        >
                        @error('email')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Minimal 8 karakter"
                            required
                        >
                        @error('password')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            Konfirmasi Password
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi password"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Role Pengguna</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="customer" @selected(old('role', 'customer') === 'customer')>
                                Customer
                            </option>
                            <option value="staff" @selected(old('role') === 'staff')>
                                Staff
                            </option>
                            <option value="admin" @selected(old('role') === 'admin')>
                                Admin
                            </option>
                        </select>
                        @error('role')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="is_active" class="form-label">Status Akun</label>
                        <select id="is_active" name="is_active" class="form-control" required>
                            <option value="1" @selected(old('is_active', '1') == '1')>
                                Aktif
                            </option>
                            <option value="0" @selected(old('is_active') == '0')>
                                Tidak Aktif
                            </option>
                        </select>
                        @error('is_active')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="button button-secondary">
                        Batal
                    </a>

                    <button type="submit" class="button button-primary">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('layouts.admin')

@section('title', 'Edit Pengguna')
@section('eyebrow', 'MANAJEMEN PENGGUNA')
@section('page-title', 'Edit Pengguna')
@section('page-description', 'Perbarui informasi akun pengguna.')

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
                <p>Mengubah akun milik {{ $user->name }}.</p>
            </div>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $user->name) }}"
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
                            value="{{ old('email', $user->email) }}"
                            placeholder="nama@email.com"
                            required
                        >
                        @error('email')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Kosongkan jika tidak diubah"
                        >
                        <small class="form-hint">Minimal 8 karakter jika ingin mengganti password.</small>
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
                        >
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Role Pengguna</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="customer" @selected(old('role', $user->role) === 'customer')>
                                Customer
                            </option>
                            <option value="staff" @selected(old('role', $user->role) === 'staff')>
                                Staff
                            </option>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>
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
                            <option value="1" @selected(old('is_active', (string) (int) $user->is_active) == '1')>
                                Aktif
                            </option>
                            <option value="0" @selected(old('is_active', (string) (int) $user->is_active) == '0')>
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
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
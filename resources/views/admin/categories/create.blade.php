@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('eyebrow', 'DATA MASTER')
@section('page-title', 'Tambah Kategori')
@section('page-description', 'Tambahkan kategori baru untuk mengelompokkan daftar menu.')

@section('page-actions')
    <a href="{{ route('admin.categories.index') }}" class="button button-secondary">
        Kembali
    </a>
@endsection

@section('content')
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2>Informasi Kategori</h2>
                <p>Lengkapi nama dan deskripsi singkat kategori.</p>
            </div>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Bakso Spesial"
                            required
                        >
                        @error('name')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Deskripsi (Opsional)</label>
                        <textarea
                            id="description"
                            name="description"
                            class="form-control"
                            rows="3"
                            placeholder="Deskripsi singkat kategori..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.categories.index') }}" class="button button-secondary">
                        Batal
                    </a>
                    <button type="submit" class="button button-primary">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
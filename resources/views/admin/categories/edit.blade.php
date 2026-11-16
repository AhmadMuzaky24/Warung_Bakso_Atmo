@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('eyebrow', 'DATA MASTER')
@section('page-title', 'Edit Kategori')
@section('page-description', 'Perbarui informasi kategori menu Bakso Atmo.')

@section('page-actions')
    <a href="{{ route('admin.categories.index') }}" class="button button-secondary">
        Kembali
    </a>
@endsection

@section('content')
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2>Edit Informasi Kategori</h2>
                <p>Ubah data kategori yang dipilih.</p>
            </div>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $category->name) }}"
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
                        >{{ old('description', $category->description) }}</textarea>
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
                        Perbarui Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
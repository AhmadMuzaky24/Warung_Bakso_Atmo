@extends('layouts.admin')

@section('title', 'Edit Menu')
@section('eyebrow', 'DATA MASTER')
@section('page-title', 'Edit Menu')
@section('page-description', 'Perbarui detail, harga, ketersediaan, atau foto menu.')

@section('page-actions')
    <a href="{{ route('admin.menus.index') }}" class="button button-secondary">
        Kembali
    </a>
@endsection

@section('content')
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2>Edit Informasi Menu</h2>
                <p>Ubah data menu yang dipilih.</p>
            </div>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $menu->category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">Nama Menu</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $menu->name) }}"
                            required
                        >
                        @error('name')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="price" class="form-label">Harga (Rp)</label>
                        <input
                            type="number"
                            id="price"
                            name="price"
                            class="form-control"
                            value="{{ old('price', $menu->price) }}"
                            min="0"
                            step="1"
                            required
                        >
                        @error('price')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="is_available" class="form-label">Ketersediaan</label>
                        <select id="is_available" name="is_available" class="form-control" required>
                            <option value="1" @selected(old('is_available', (int) $menu->is_available) == 1)>Tersedia</option>
                            <option value="0" @selected(old('is_available', (int) $menu->is_available) == 0)>Tidak Tersedia</option>
                        </select>
                        @error('is_available')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label">Ganti Foto Menu (Opsional)</label>
                        <input
                            type="file"
                            id="image"
                            name="image"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                        >
                        <small class="form-hint">Format JPG, PNG, atau WEBP maksimal 2 MB.</small>
                        @error('image')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror

                        @if ($menu->image_path)
                            <div style="margin-top: 10px;">
                                <img
                                    src="{{ asset('storage/' . $menu->image_path) }}"
                                    alt="{{ $menu->name }}"
                                    style="width: 96px; height: 96px; object-fit: cover; border-radius: 8px; display: block; margin-bottom: 6px;"
                                >
                                <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.875rem;">
                                    <input type="checkbox" name="remove_image" value="1">
                                    Hapus foto saat ini
                                </label>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Deskripsi (Opsional)</label>
                        <textarea
                            id="description"
                            name="description"
                            class="form-control"
                            rows="3"
                        >{{ old('description', $menu->description) }}</textarea>
                        @error('description')
                            <small class="form-hint" style="color: var(--color-danger);">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.menus.index') }}" class="button button-secondary">
                        Batal
                    </a>
                    <button type="submit" class="button button-primary">
                        Perbarui Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('layouts.admin')

@section('title', 'Daftar Menu')
@section('eyebrow', 'DATA MASTER')
@section('page-title', 'Daftar Menu')
@section('page-description', 'Kelola daftar menu, harga, foto, dan ketersediaan Bakso Atmo.')

@section('page-actions')
    <a href="{{ route('admin.menus.create') }}" class="button button-primary">
        + Tambah Menu
    </a>
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

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Daftar Menu</h2>
                <p>Menu yang tampil di katalog publik dan pilihan pemesanan katering.</p>
            </div>
            <span class="badge badge-active">
                {{ $menus->total() }} Menu
            </span>
        </div>

        <div class="panel-body">
            <form method="GET" action="{{ route('admin.menus.index') }}" class="search-form">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cari nama menu..."
                    value="{{ $search }}"
                >

                <select name="category_id" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="button button-primary">
                    Filter
                </button>

                @if ($search || $categoryId !== '')
                    <a href="{{ route('admin.menus.index') }}" class="button button-secondary">
                        Reset
                    </a>
                @endif
            </form>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Foto</th>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($menus as $menu)
                            <tr>
                                <td>{{ $menus->firstItem() + $loop->index }}</td>
                                <td>
                                    @if ($menu->image_path)
                                        <img
                                            src="{{ asset('storage/' . $menu->image_path) }}"
                                            alt="{{ $menu->name }}"
                                            style="width: 56px; height: 56px; object-fit: cover; border-radius: 8px;"
                                        >
                                    @else
                                        <span class="table-secondary">Tanpa foto</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-primary">{{ $menu->name }}</div>
                                    <div class="table-secondary">
                                        {{ $menu->category?->name ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($menu->price, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if ($menu->is_available)
                                        <span class="badge badge-active">Tersedia</span>
                                    @else
                                        <span class="badge badge-inactive">Habis / Tidak Tersedia</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a
                                            href="{{ route('admin.menus.edit', $menu) }}"
                                            class="button button-secondary button-small"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('admin.menus.destroy', $menu) }}"
                                            method="POST"
                                            class="inline-form"
                                            onsubmit="return confirm('Yakin ingin menghapus menu ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="button button-danger button-small">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div style="padding: 28px 12px; text-align: center;">
                                        <strong>Tidak ada menu ditemukan.</strong>
                                        <p style="color: var(--color-muted); margin-top: 6px;">
                                            Tambahkan menu baru atau sesuaikan filter pencarian.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($menus->hasPages())
                <div class="pagination">
                    {{ $menus->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
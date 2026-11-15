@extends('layouts.admin')

@section('title', 'Kategori Menu')
@section('eyebrow', 'DATA MASTER')
@section('page-title', 'Kategori Menu')
@section('page-description', 'Kelola kategori untuk pengelompokan menu Bakso Atmo.')

@section('page-actions')
    <a href="{{ route('admin.categories.create') }}" class="button button-primary">
        + Tambah Kategori
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
                <h2>Daftar Kategori</h2>
                <p>Kategori menu yang tersedia pada sistem Bakso Atmo.</p>
            </div>
            <span class="badge badge-active">
                {{ $categories->total() }} Kategori
            </span>
        </div>

        <div class="panel-body">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="search-form">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cari nama kategori..."
                    value="{{ $search }}"
                >

                <button type="submit" class="button button-primary">
                    Cari
                </button>

                @if ($search)
                    <a href="{{ route('admin.categories.index') }}" class="button button-secondary">
                        Reset
                    </a>
                @endif
            </form>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Menu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $categories->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="table-primary">{{ $category->name }}</div>
                                </td>
                                <td>
                                    <div class="table-secondary">
                                        {{ $category->description ?: '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-active">
                                        {{ $category->menus_count }} Menu
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a
                                            href="{{ route('admin.categories.edit', $category) }}"
                                            class="button button-secondary button-small"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('admin.categories.destroy', $category) }}"
                                            method="POST"
                                            class="inline-form"
                                            onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
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
                                <td colspan="5">
                                    <div style="padding: 28px 12px; text-align: center;">
                                        <strong>Tidak ada kategori ditemukan.</strong>
                                        <p style="color: var(--color-muted); margin-top: 6px;">
                                            Silakan tambahkan kategori baru atau ubah kata kunci pencarian.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="pagination">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
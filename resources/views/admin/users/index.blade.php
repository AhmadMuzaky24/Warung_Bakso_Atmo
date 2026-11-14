@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('eyebrow', 'PENGELOLAAN AKUN')
@section('page-title', 'Manajemen Pengguna')
@section('page-description', 'Kelola akun, hak akses, dan status pengguna sistem Bakso Atmo.')

@section('page-actions')
    <a href="{{ route('admin.users.create') }}" class="button button-primary">
        + Tambah Pengguna
    </a>
@endsection

@section('content')

    {{-- Pesan validasi --}}
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
                <h2>Daftar Pengguna</h2>
                <p>Pengguna yang terdaftar dalam sistem Bakso Atmo.</p>
            </div>
            <span class="badge badge-active">
                {{ $users->total() }} Pengguna
            </span>
        </div>

        <div class="panel-body">
            {{-- Form pencarian --}}
            <form
                method="GET"
                action="{{ route('admin.users.index') }}"
                class="search-form"
            >
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cari nama atau email pengguna..."
                    value="{{ $search }}"
                >

                <button type="submit" class="button button-primary">
                    Cari
                </button>

                @if ($search)
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="button button-secondary"
                    >
                        Reset
                    </a>
                @endif
            </form>

            {{-- Tabel pengguna --}}
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    {{ $users->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <div class="table-primary">
                                        {{ $user->name }}
                                    </div>
                                    <div class="table-secondary">
                                        {{ $user->email }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge badge-{{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>

                                <td>
                                    @if ($user->is_active)
                                        <span class="badge badge-active">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-inactive">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="table-actions">
                                        <a
                                            href="{{ route('admin.users.edit', $user) }}"
                                            class="button button-secondary button-small"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            method="POST"
                                            class="inline-form"
                                            onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="button button-danger button-small"
                                            >
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
                                        <strong>Tidak ada pengguna ditemukan.</strong>
                                        <p style="color: var(--color-muted); margin-top: 6px;">
                                            Coba gunakan kata kunci pencarian yang berbeda.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="pagination">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </section>

@endsection
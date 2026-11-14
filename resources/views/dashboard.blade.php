@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'OVERVIEW')
@section('page-title', 'Dashboard')
@section('page-description', 'Selamat datang di ruang kerja Bakso Atmo.')

@section('content')

    <section class="welcome-panel">
        <div>
            <span class="page-eyebrow" style="color: #e6b49f;">
                SELAMAT DATANG
            </span>

            <h2>Halo, {{ auth()->user()->name }}!</h2>

            <p>
                Selamat bekerja. Semoga aktivitas hari ini berjalan lancar.
            </p>
        </div>

        <div class="welcome-mark">BA</div>
    </section>

    <section class="info-grid">
        <article class="info-card">
            <small>Nama Pengguna</small>
            <strong>{{ auth()->user()->name }}</strong>
        </article>

        <article class="info-card">
            <small>Alamat Email</small>
            <strong>{{ auth()->user()->email }}</strong>
        </article>

        <article class="info-card">
            <small>Role Akun</small>
            <strong>{{ ucfirst(auth()->user()->role) }}</strong>
        </article>

        <article class="info-card">
            <small>Status Akun</small>
            <strong>
                <span class="badge badge-active">Aktif</span>
            </strong>
        </article>
    </section>

    <div style="margin-top: 24px;">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2>Informasi Sistem</h2>
                    <p>Informasi dasar aplikasi Bakso Atmo.</p>
                </div>
            </div>

            <div class="panel-body">
                <p style="margin: 0; color: var(--color-muted);">
                    Dashboard ini merupakan fondasi awal sistem manajemen
                    Bakso Atmo. Fitur operasional, transaksi, dan laporan
                    akan dikembangkan pada sprint berikutnya.
                </p>
            </div>
        </div>
    </div>

@endsection
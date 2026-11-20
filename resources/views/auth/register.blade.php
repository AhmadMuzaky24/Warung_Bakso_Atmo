<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Daftar Akun - Bakso Atmo</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="auth-page">
        <section class="auth-card">
            <a href="{{ route('login') }}" class="auth-brand">
                <span class="brand-mark">BA</span>
                <span class="brand-copy">
                    <strong>Bakso Atmo</strong>
                    <small>Management System</small>
                </span>
            </a>

            <div class="auth-description">
                <span class="page-eyebrow">DAFTAR AKUN BARU</span>
                <h1>Buat Akun Baru</h1>
                <p>Akun ini dipakai untuk booking katering dan memantau status pesananmu.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <span>!</span>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Nama lengkap kamu"
                        autocomplete="name"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Nomor HP (10-13 digit, diawali 08 atau +62)</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-control"
                        value="{{ old('phone') }}"
                        placeholder="Contoh: 081234567890"
                        inputmode="tel"
                        autocomplete="tel"
                        maxlength="20"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password (minimal 8 karakter)</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Ulangi password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit" class="button button-primary" style="width: 100%;">
                    Daftar Sekarang
                </button>

                <p>
                    Sudah punya akun?
                    <a href="{{ route('login') }}" style="color: var(--color-primary); text-decoration: underline;">Masuk</a>
                </p>
            </form>

            <div class="auth-footer">
                © {{ date('Y') }} Bakso Atmo. All rights reserved.
            </div>
        </section>
    </main>
</body>
</html>

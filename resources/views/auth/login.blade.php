<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Bakso Atmo</title>

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
                <span class="page-eyebrow">SELAMAT DATANG KEMBALI</span>
                <h1>Masuk ke Akun</h1>
                <p>Silakan masuk untuk mengakses sistem manajemen Bakso Atmo.</p>
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

            <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        autocomplete="email"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <label class="checkbox-row">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        @checked(old('remember'))
                    >
                    <span>Ingat saya</span>
                </label>

                <button type="submit" class="button button-primary" style="width: 100%;">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="auth-footer">
                © {{ date('Y') }} Bakso Atmo. All rights reserved.
            </div>
        </section>
    </main>
</body>
</html>
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Http\Requests\Auth\ChangePasswordRequest;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();
        $identifier = $credentials['login'];
        $phone = PhoneNumber::normalize($identifier);

        $key = Str::transliterate(
            Str::lower($phone ?? $identifier).'|'.$request->ip()
        );

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'login' => 'Terlalu banyak percobaan login. Silakan coba kembali beberapa saat lagi.',
            ]);
        }

        // Email dicoba lebih dulu (admin/staff tidak berubah), lalu nomor HP.
        // Dua varian email: apa adanya + lowercase, karena MySQL (CI) dan SQLite (BINARY) beda perilaku.
        $emails = array_unique([trim($identifier), Str::lower(trim($identifier))]);

        $attempts = array_map(static fn (string $email): array => ['email' => $email], $emails);

        if ($phone !== null) {
            $attempts[] = ['phone' => $phone];
        }

        $authenticated = false;

        foreach ($attempts as $fields) {
            if (Auth::attempt($fields + [
                'password' => $credentials['password'],
                'is_active' => true,
            ], $request->boolean('remember'))) {
                $authenticated = true;
                break;
            }
        }

        if (! $authenticated) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'login' => 'Email/nomor HP atau password tidak valid, atau akun tidak aktif.',
            ]);
        }

        RateLimiter::clear($key);

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $phone = $request->validated('phone');

        $user = User::create([
            'name' => $request->validated('name'),
            'phone' => $phone,
            'email' => $phone.'@'.config('bakso_atmo.registration.email_placeholder'),
            'password' => $request->validated('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('customer.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showChangePasswordForm(): View
    {
        return view('auth.change-password');
    }

    public function updatePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return redirect()
            ->route('password.edit')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
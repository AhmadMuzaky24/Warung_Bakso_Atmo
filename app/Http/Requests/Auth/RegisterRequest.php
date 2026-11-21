<?php

namespace App\Http\Requests\Auth;

use App\Rules\ValidPhoneNumber;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalized = PhoneNumber::normalize(is_string($this->phone) ? $this->phone : null);

        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            // Nilai asli dipertahankan kalau tidak valid, supaya rule yang melempar error (bukan "required").
            'phone' => $normalized ?? $this->phone,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', new ValidPhoneNumber, 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.max' => 'Nomor HP maksimal 20 karakter.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar. Silakan masuk.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'phone' => 'nomor HP',
            'password' => 'password',
        ];
    }
}

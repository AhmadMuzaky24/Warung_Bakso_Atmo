<?php

namespace App\Http\Requests\Catering;

use App\Rules\ValidPhoneNumber;
use App\Support\BookingWindow;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCateringOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->customer_phone)) {
            // Simpan format seragam; nilai asli dipertahankan kalau tidak valid biar rule yang menolak.
            $this->merge([
                'customer_phone' => PhoneNumber::normalize($this->customer_phone) ?? $this->customer_phone,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'event_date' => ['required', 'date', 'after_or_equal:'.BookingWindow::earliestEventDate()->toDateString()],
            'event_address' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20', new ValidPhoneNumber],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', Rule::exists('menus', 'id')->where('is_available', true)],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_date.required' => 'Tanggal acara wajib diisi.',
            'event_date.date' => 'Format tanggal acara tidak valid.',
            'event_date.after_or_equal' => 'Tanggal acara paling cepat :date. Pemesanan untuk H-1 ditutup pukul '.BookingWindow::cutoffTime().' WIB.',
            'event_address.required' => 'Alamat acara wajib diisi.',
            'event_address.max' => 'Alamat acara maksimal 255 karakter.',
            'customer_phone.required' => 'Nomor HP wajib diisi.',
            'customer_phone.max' => 'Nomor HP maksimal 20 karakter.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
            'items.required' => 'Pilih minimal satu menu katering.',
            'items.min' => 'Pilih minimal satu menu katering.',
            'items.*.menu_id.required' => 'Menu wajib dipilih.',
            'items.*.menu_id.exists' => 'Menu yang dipilih tidak tersedia.',
            'items.*.quantity.required' => 'Jumlah porsi wajib diisi.',
            'items.*.quantity.min' => 'Jumlah porsi minimal 1.',
            'items.*.quantity.max' => 'Jumlah porsi maksimal 500.',
        ];
    }

    public function attributes(): array
    {
        return [
            'event_date' => 'tanggal acara',
            'event_address' => 'alamat acara',
            'customer_phone' => 'nomor HP',
            'notes' => 'catatan',
            'items' => 'daftar menu',
            'items.*.menu_id' => 'menu',
            'items.*.quantity' => 'jumlah porsi',
        ];
    }
}

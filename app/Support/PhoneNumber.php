<?php

namespace App\Support;

class PhoneNumber
{
    /**
     * Ubah input apa pun yang valid jadi format 08xxxxxxxxxx, atau null kalau tidak valid.
     */
    public static function normalize(?string $raw): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $raw);

        if (str_starts_with($digits, '62')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (! str_starts_with($digits, '8')) {
            return null;
        }

        $local = '0'.$digits;

        return preg_match('/^08[0-9]{8,11}$/', $local) === 1 ? $local : null;
    }

    /**
     * 081234567890 -> 6281234567890 (untuk link wa.me).
     */
    public static function toInternational(string $phone): string
    {
        return '62'.substr($phone, 1);
    }
}

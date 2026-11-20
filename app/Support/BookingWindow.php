<?php

namespace App\Support;

use Carbon\Carbon;
use Throwable;

class BookingWindow
{
    /**
     * Jam cutoff hari-H untuk pemesanan H-1, contoh '14:30'.
     */
    public static function cutoffTime(): string
    {
        $raw = (string) config('bakso_atmo.booking.cutoff_time', '14:30');

        try {
            return Carbon::createFromFormat('H:i', $raw)->format('H:i');
        } catch (Throwable) {
            // .env typo tidak boleh bikin seluruh form booking 500.
            return '14:30';
        }
    }

    /**
     * Tanggal acara paling awal yang masih boleh dipesan.
     */
    public static function earliestEventDate(?Carbon $now = null): Carbon
    {
        $now = $now ?? Carbon::now();
        $minDays = (int) config('bakso_atmo.booking.min_days_before', 1);

        $cutoff = $now->copy()->startOfDay()
            ->setTimeFromTimeString(static::cutoffTime());

        $days = $minDays;

        if ($now->greaterThan($cutoff)) {
            $days++;
        }

        return $now->copy()->startOfDay()->addDays($days);
    }

    /**
     * Apakah tanggal acara ini masih boleh dipesan?
     */
    public static function isDateAllowed(Carbon|string $date, ?Carbon $now = null): bool
    {
        return Carbon::parse($date)->startOfDay()
            ->greaterThanOrEqualTo(static::earliestEventDate($now));
    }
}

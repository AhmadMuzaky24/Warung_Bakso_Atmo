<?php

namespace Tests\Unit;

use App\Support\BookingWindow;
use Carbon\Carbon;
use Tests\TestCase;

class BookingWindowTest extends TestCase
{
    private function at(string $moment): Carbon
    {
        return Carbon::parse($moment);
    }

    public function test_before_cutoff_earliest_is_tomorrow(): void
    {
        $this->assertSame(
            '2026-11-21',
            BookingWindow::earliestEventDate($this->at('2026-11-20 13:00:00'))->toDateString()
        );
    }

    public function test_exactly_at_cutoff_still_counts_as_before(): void
    {
        $this->assertSame(
            '2026-11-21',
            BookingWindow::earliestEventDate($this->at('2026-11-20 14:30:00'))->toDateString()
        );
    }

    public function test_one_second_after_cutoff_shifts_one_day(): void
    {
        $this->assertSame(
            '2026-11-22',
            BookingWindow::earliestEventDate($this->at('2026-11-20 14:30:01'))->toDateString()
        );
    }

    public function test_late_afternoon_shifts_one_day(): void
    {
        $this->assertSame(
            '2026-11-22',
            BookingWindow::earliestEventDate($this->at('2026-11-20 17:00:00'))->toDateString()
        );
    }

    public function test_is_date_allowed_follows_cutoff(): void
    {
        $late = $this->at('2026-11-20 17:00:00');

        $this->assertFalse(BookingWindow::isDateAllowed('2026-11-21', $late));
        $this->assertTrue(BookingWindow::isDateAllowed('2026-11-22', $late));
        $this->assertFalse(BookingWindow::isDateAllowed('2026-11-20', $this->at('2026-11-20 08:00:00')));
    }

    public function test_cutoff_time_falls_back_on_invalid_config(): void
    {
        config()->set('bakso_atmo.booking.cutoff_time', '14.30');

        $this->assertSame('14:30', BookingWindow::cutoffTime());
    }
}

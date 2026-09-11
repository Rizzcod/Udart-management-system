<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;

/**
 * The sample data was written as of 14 June 2026. Shifting its fixed dates by
 * the days elapsed since then keeps the seeded scenario (overdue services,
 * repairs in progress, recent history) accurate whenever the database is seeded.
 */
class SeedDate
{
    public const REFERENCE = '2026-06-14';

    public static function shift(?string $date): ?string
    {
        if ($date === null) {
            return null;
        }

        $offset = (int) Carbon::parse(self::REFERENCE)->diffInDays(today(), false);

        return Carbon::parse($date)->addDays($offset)->toDateString();
    }
}

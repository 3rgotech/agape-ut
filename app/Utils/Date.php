<?php

namespace App\Utils;

use Illuminate\Support\Carbon;

class Date
{
    public static function parse(string $date): ?Carbon
    {
        $formats = [
            'Y-m-d',
            'd/m/Y',
        ];
        foreach ($formats as $format) {
            if (Carbon::hasFormat($date, $format) && Carbon::createFromFormat($format, $date)->format($format) === $date) {
                return Carbon::createFromFormat($format, $date);
            }
        }
        if (strtotime($date) !== false) {
            return Carbon::createFromTimestamp(strtotime($date));
        }
        return null;
    }

    public static function isValid(string $date): bool
    {
        return self::parse($date) !== null;
    }
}

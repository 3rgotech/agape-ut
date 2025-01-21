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
            if (Carbon::hasFormat($date, $format)) {
                return Carbon::createFromFormat($format, $date);
            }
        }
        return null;
    }
}

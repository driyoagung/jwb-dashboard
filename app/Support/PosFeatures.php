<?php

namespace App\Support;

class PosFeatures
{
    public static function customersEnabled(): bool
    {
        return (bool) config('pos.features.customers', false);
    }

    public static function creditEnabled(): bool
    {
        return self::customersEnabled() && (bool) config('pos.features.credit', false);
    }
}

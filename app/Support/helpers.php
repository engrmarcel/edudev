<?php

use App\Services\Branding\BrandManager;

if (! function_exists('brand')) {
    /**
     * brand() returns the BrandManager; brand('product_name') returns one value.
     */
    function brand(?string $key = null, mixed $default = null): mixed
    {
        $manager = app(BrandManager::class);

        return $key === null ? $manager : $manager->get($key, $default);
    }
}

if (! function_exists('money_format_minor')) {
    /**
     * Format an amount held in MINOR units (e.g. kobo) as a currency string.
     * Money is always stored as integers in minor units, never as floats.
     */
    function money_format_minor(int $minorUnits, ?string $currency = null): string
    {
        $currency ??= config('edudev.locale.currency', 'NGN');

        if (class_exists(NumberFormatter::class)) {
            $fmt = new NumberFormatter(config('edudev.locale.currency_locale', 'en_NG'), NumberFormatter::CURRENCY);
            $out = $fmt->formatCurrency($minorUnits / 100, $currency);
            if ($out !== false) {
                return $out;
            }
        }

        $sign = $minorUnits < 0 ? '-' : '';
        $abs = abs($minorUnits);

        return $sign.$currency.' '.number_format(intdiv($abs, 100)).'.'.str_pad((string) ($abs % 100), 2, '0', STR_PAD_LEFT);
    }
}

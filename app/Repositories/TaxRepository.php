<?php

declare(strict_types=1);

namespace App\Repositories;

use Lunar\Models\TaxClass;

class TaxRepository
{
    private static ?TaxClass $defaultShippingTaxClass = null;

    public function getDefaultShippingTaxClass(): TaxClass
    {
        return self::$defaultShippingTaxClass ??= TaxClass::query()
            ->where('name', 'Shipping')
            ->first() ?? TaxClass::query()
            ->where('default', true)
            ->firstOrFail();
    }
}

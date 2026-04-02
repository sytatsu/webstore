<?php

declare(strict_types=1);

namespace App\Repositories;

use Lunar\Models\TaxClass;

class TaxRepository
{
    public function getDefaultShippingTaxClass(): TaxClass
    {
        return TaxClass::query()
            ->where('name', 'Shipping')
            ->first() ?? TaxClass::query()
            ->where('default', true)
            ->firstOrFail();
    }
}

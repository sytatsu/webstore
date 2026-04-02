<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\CartService;
use Lunar\Models\TaxClass;

trait TaxTrait
{
    protected function getDefaultTaxClass(): TaxClass
    {
        return app(CartService::class)->getDefaultShippingTaxClass();
    }
}

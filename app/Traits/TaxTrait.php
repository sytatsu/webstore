<?php

declare(strict_types=1);

namespace App\Traits;

use Lunar\Models\TaxClass;

trait TaxTrait
{
    private TaxClass $defaultTaxClass;

    protected function getDefaultTaxClass(): TaxClass
    {
        return $this->defaultTaxClass ?? $this->defaultTaxClass = TaxClass::query()
            ->where('default', true)
            ->firstOrFail();
    }
}

<?php

namespace App\Observers;

use Lunar\Models\ProductVariant;

class ProductVariantObserver
{
    /**
     * Handle the ProductVariant "creating" event.
     */
    public function creating(ProductVariant $productVariant): void
    {
        if (empty($productVariant->purchasable)) {
            $productVariant->purchasable = 'in_stock';
        }
    }
}

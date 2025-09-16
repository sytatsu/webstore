<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;

/**
 * @property Cart $cart
 */
class ShippingService
{
    public function getAvailableShippingOptions(Cart $cart): Collection
    {
        return $this->getShippingOptionsFromManifest($cart)->filter(function (ShippingOption $shippingOption) use ($cart) {
            if ($this->canHaveFreeShipping($cart)) {
                return $shippingOption->identifier === 'FREETARDEL';
            }

            return $shippingOption->identifier !== 'FREETARDEL';
        })->unique('identifier');
    }

    public function getDefaultShippingOption(Cart $cart): mixed
    {
        return $this->getAvailableShippingOptions($cart)->first();
    }

    private function canHaveFreeShipping (Cart $cart): bool
    {
        return $cart->subTotalDiscounted->value > 1000;
    }

    private function getShippingOptionsFromManifest($cart): Collection
    {
        return ShippingManifest::getOptions($cart);
    }
}

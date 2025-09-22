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

    public function getShippingOption(Cart $cart): ShippingOption
    {
        return $cart->getShippingOption();
    }

    public function recalculateShippingOption(Cart $cart): ShippingOption|\Closure
    {
        $shippingOptions = $this->getAvailableShippingOptions($cart);

        if ($cart->getShippingOption()) {
            return $shippingOptions->has(['identifier' => $cart->getShippingOption()->getIdentifier()])
                ? $shippingOptions->first(['identifier' => $cart->getShippingOption()->getIdentifier()])
                : $shippingOptions->first();
        }

        return $shippingOptions->first();
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

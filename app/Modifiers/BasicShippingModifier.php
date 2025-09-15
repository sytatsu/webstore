<?php

declare(strict_types=1);

namespace App\Modifiers;

use App\Traits\TaxTrait;
use Closure;
use Lunar\Base\ShippingModifier;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Contracts\Cart;

class BasicShippingModifier extends ShippingModifier
{
    use TaxTrait;

    public function handle(Cart $cart, Closure $next)
    {
        ShippingManifest::addOption(
            new ShippingOption(
                name: 'Basic Delivery',
                description: 'Sending items in 2-3 business days',
                identifier: 'BASDEL',
                price: new Price(config('lunar.shipping.delivery_rates.BASDEL'), $cart->currency, 1),
                taxClass: $this->getDefaultTaxClass()
            )
        );

        ShippingManifest::addOption(
            new ShippingOption(
                name: 'Express Delivery',
                description: 'Sending items within 1 business day',
                identifier: 'EXDEL',
                price: new Price(config('lunar.shipping.delivery_rates.EXDEL'), $cart->currency, 1),
                taxClass: $this->getDefaultTaxClass()
            )
        );

        return $next($cart);
    }
}

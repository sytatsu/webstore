<?php

declare(strict_types=1);

namespace App\Modifiers;

use App\Traits\TaxTrait;
use Closure;
use FontLib\Table\Type\name;
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
        ShippingManifest::addOptions(collect([
                new ShippingOption(
                    name: 'Basic Delivery',
                    description: 'Sending items in 1-2 business days without Track & Trace',
                    identifier: 'BASDEL',
                    price: new Price(config('lunar.shipping.delivery_rates.BASDEL'), $cart->currency, 1),
                    taxClass: $this->getDefaultTaxClass()
                ),
                new ShippingOption(
                    name: 'Tracked Delivery',
                    description: 'Sending items within 1-2 business days with Track & Trace',
                    identifier: 'TARDEL',
                    price: new Price(config('lunar.shipping.delivery_rates.TARDEL'), $cart->currency, 1),
                    taxClass: $this->getDefaultTaxClass()
                ),
                new ShippingOption(
                    name: 'Free Tracked Delivery',
                    description: 'Sending items within 1-2 business days with Track & Trace',
                    identifier: 'FREETARDEL',
                    price: new Price(0, $cart->currency, 1),
                    taxClass: $this->getDefaultTaxClass()
                ),
            ])
        );

        return $next($cart);
    }
}

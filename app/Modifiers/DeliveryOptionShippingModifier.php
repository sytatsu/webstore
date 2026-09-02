<?php

declare(strict_types=1);

namespace App\Modifiers;

use App\Models\DeliveryOption;
use App\Traits\TaxTrait;
use Closure;
use Lunar\Base\ShippingModifier;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Contracts\Cart;

class DeliveryOptionShippingModifier extends ShippingModifier
{
    use TaxTrait;

    public function handle(Cart $cart, Closure $next)
    {
        $options = DeliveryOption::enabled()->ordered()->get()->map(
            fn (DeliveryOption $option) => new ShippingOption(
                name: $option->name,
                description: $option->description,
                identifier: $option->identifier,
                price: new Price($option->price, $cart->currency, 1),
                taxClass: $this->getDefaultTaxClass(),
                meta: ['free_shipping' => $option->free_shipping],
            )
        );

        ShippingManifest::addOptions($options);

        return $next($cart);
    }
}

<?php

declare(strict_types=1);

namespace App\Modifiers;

use App\Models\PickupLocation;
use App\Traits\TaxTrait;
use Closure;
use Lunar\Base\ShippingModifier;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Contracts\Cart;

class PickupShippingModifier extends ShippingModifier
{
    use TaxTrait;

    public function handle(Cart $cart, Closure $next)
    {
        $options = PickupLocation::enabled()->ordered()->get()->map(
            fn (PickupLocation $location) => new ShippingOption(
                name: "Pick-up – {$location->name}",
                description: trim("{$location->full_address}. {$location->translate('availability_note')}"),
                identifier: $location->identifier,
                price: new Price($location->price, $cart->currency, 1),
                taxClass: $this->getDefaultTaxClass(),
                collect: true,
                meta: ['pickup_location_id' => $location->id],
            )
        );

        ShippingManifest::addOptions($options);

        return $next($cart);
    }
}

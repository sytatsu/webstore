<?php

namespace App\Validation\CartLine;

use App\Support\PrintOnDemand;
use Lunar\Validation\CartLine\CartLineStock as LunarCartLineStock;

/**
 * Lunar's ProductVariant::canBeFulfilledAtQuantity() only knows 'always' as
 * unlimited, so it would cap a print-on-demand variant at stock + backorder.
 */
class CartLineStock extends LunarCartLineStock
{
    public function validate(): bool
    {
        $purchasable = $this->parameters['purchasable'] ?? null;
        $cartLineId = $this->parameters['cartLineId'] ?? null;
        $cart = $this->parameters['cart'] ?? null;

        if ($cartLineId && ! $purchasable && $cart) {
            $purchasable = $cart->lines->first(
                fn ($cartLine) => $cartLine->id == $cartLineId
            )?->purchasable;
        }

        if (PrintOnDemand::applies($purchasable)) {
            return $this->pass();
        }

        return parent::validate();
    }
}

<?php

namespace App\Pipelines\Lunar\CartLine;

use Closure;
use Lunar\DataTypes\Price;
use Lunar\Models\CartLine;

class ApplyBundleDiscount
{
    public function handle(CartLine $cartLine, Closure $next): CartLine
    {
        $cartLine = $next($cartLine);

        $pct = data_get($cartLine->meta, 'bundle_discount_pct');

        if ($pct && $cartLine->unitPrice) {
            $discountedValue = (int) round($cartLine->unitPrice->value * (1 - $pct / 100));
            $cartLine->unitPrice = new Price(
                $discountedValue,
                $cartLine->unitPrice->currency,
                $cartLine->unitPrice->unitQty
            );
        }

        return $cartLine;
    }
}

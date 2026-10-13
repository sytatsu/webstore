<?php

namespace App\Support;

use Lunar\Base\Purchasable;
use Lunar\Models\ProductVariant;

/**
 * The 'in_stock_or_print_on_demand' purchasability: a variant can always be
 * ordered, but anything beyond its current stock is printed on demand. Unlike
 * Lunar's own 'always', the customer is told which part that applies to —
 * on the product page, the tile, the cart and the order confirmation email.
 */
class PrintOnDemand
{
    public const PURCHASABILITY = 'in_stock_or_print_on_demand';

    /**
     * Set on an order line's meta by DecreaseStock, at the moment the order is
     * placed — the variant's stock has already changed by the time anything
     * (the confirmation email) renders that order line.
     */
    public const META_KEY = 'print_on_demand_quantity';

    public static function applies(?Purchasable $purchasable): bool
    {
        return $purchasable instanceof ProductVariant
            && $purchasable->purchasable === self::PURCHASABILITY;
    }

    /**
     * How many of $quantity cannot be served from stock and will be printed.
     */
    public static function quantity(?Purchasable $purchasable, int $quantity): int
    {
        if (! self::applies($purchasable)) {
            return 0;
        }

        return max(0, $quantity - max(0, (int) $purchasable->stock));
    }

    /**
     * Whether ordering one more right now would need printing.
     */
    public static function isOutOfStock(?Purchasable $purchasable): bool
    {
        return self::applies($purchasable) && (int) $purchasable->stock <= 0;
    }
}

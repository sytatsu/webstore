<?php

namespace App\Pipelines\Lunar\Order\Creation;

use App\Support\PrintOnDemand;
use Closure;
use Lunar\Models\CartLine;
use Lunar\Models\Contracts\Order as OrderContract;

class DecreaseStock
{
    public function handle(OrderContract $order, Closure $next)
    {
        $order->cart->calculate()->lines->each(function (CartLine $line) use ($order) {
            if ($line->purchasable->purchasable === 'in_stock') {
                $line->purchasable->decrement('stock', $line->quantity);
            }

            if (PrintOnDemand::applies($line->purchasable)) {
                $this->decreasePrintOnDemandStock($order, $line);
            }
        });

        return $next($order);
    }

    /**
     * Only what is actually on the shelf comes out of stock; the rest is
     * printed. That split is stamped on the order line, because stock will
     * have changed by the time the confirmation email renders it.
     */
    private function decreasePrintOnDemandStock(OrderContract $order, CartLine $line): void
    {
        $printQuantity = PrintOnDemand::quantity($line->purchasable, $line->quantity);
        $fromStock = $line->quantity - $printQuantity;

        if ($fromStock > 0) {
            $line->purchasable->decrement('stock', $fromStock);
        }

        if ($printQuantity <= 0) {
            return;
        }

        $orderLine = $order->lines()
            ->where('purchasable_type', $line->purchasable_type)
            ->where('purchasable_id', $line->purchasable_id)
            ->where('quantity', $line->quantity)
            ->first();

        if (! $orderLine) {
            return;
        }

        $orderLine->meta = [
            ...($orderLine->meta?->getArrayCopy() ?? []),
            PrintOnDemand::META_KEY => $printQuantity,
        ];
        $orderLine->save();
    }
}

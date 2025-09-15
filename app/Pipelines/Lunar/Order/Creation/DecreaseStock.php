<?php

namespace App\Pipelines\Lunar\Order\Creation;

use Closure;
use Lunar\Models\CartLine;
use Lunar\Models\Contracts\Order as OrderContract;

class DecreaseStock
{
    public function handle(OrderContract $order, Closure $next)
    {
        $order->cart->calculate()->lines->each(function (CartLine $line) {
            if($line->purchasable->purchasable === 'in_stock') {
                $line->purchasable->decrement('stock', $line->quantity);
            }
        });

        return $next($order);
    }
}

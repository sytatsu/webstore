<?php

namespace App\Pipelines\Lunar\Order\Creation;

use Closure;
use Lunar\Models\Contracts\Order as OrderContract;

class SetOrderNotes
{
    public function handle(OrderContract $order, Closure $next)
    {
        $order->update([
            'notes' => $order->cart->meta['notes'] ?? null,
        ]);

        return $next($order);
    }
}

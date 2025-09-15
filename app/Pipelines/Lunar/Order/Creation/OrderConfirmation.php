<?php

namespace App\Pipelines\Lunar\Order\Creation;

use Closure;
use Lunar\Models\CartLine;
use Lunar\Models\Contracts\Order as OrderContract;

class OrderConfirmation
{

    public function handle(OrderContract $order, Closure $next)
    {
        // TODO; implement order confirmation

        return $next($order);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Lunar\Facades\CartSession;
use Lunar\Stripe\Enums\CancellationReason;
use Lunar\Stripe\Facades\Stripe;

class StripeIntent
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        Stripe::syncIntent(CartSession::current());

        return $next($request);
    }

}

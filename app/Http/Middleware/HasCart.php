<?php

namespace App\Http\Middleware;

use App;
use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;
use Redirect;

class HasCart
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (App::get(CartService::class)->getCurrentCart()->lines->isEmpty()) {
            return Redirect::route('sytatsu.webstore.welcome');
        }

        return $next($request);
    }
}

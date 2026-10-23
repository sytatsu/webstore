<?php

namespace App\Http\Middleware;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App;
use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;
use Redirect;

class HasCart
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (App::get(CartService::class)->getCurrentCart()->lines->isEmpty()) {
            return Redirect::route('sytatsu.webstore.welcome');
        }

        return $next($request);
    }
}

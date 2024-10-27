<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;


class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request?->user()?->isAdmin()) {
            throw new UnauthorizedException();
        }

        return $next($request);
    }
}

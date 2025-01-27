<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Locale {

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure                $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        App::setLocale(session('locale', config('app.locale')));

        return $next($request);
    }

}

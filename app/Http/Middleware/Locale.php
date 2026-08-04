<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

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
        // The URL is the source of truth for which language a page renders in: a "/en/..."
        // path (registered as ".en"-suffixed routes, see routes/sytatsu.php) means English,
        // anything else means the default (nl) at the root. This keeps nl/en as distinct,
        // crawlable URLs instead of the same URL rendering differently based on session.
        //
        // Livewire's own "/livewire/update" AJAX endpoint has no locale segment of its own,
        // so it falls back to the session value set by the last page that was loaded,
        // keeping those requests in sync with the page that made them.
        $locale = $request->is('livewire/*')
            ? session('locale', 'nl')
            : ($request->segment(1) === 'en' ? 'en' : 'nl');

        App::setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }

}

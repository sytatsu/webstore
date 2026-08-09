<?php

namespace App\Http\Middleware;

use App\Support\LocaleAwareUrlGenerator;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route as RouteFacade;

/**
 * A visitor who was previously on the English site (session/last request resolved to "en")
 * but lands on a plain, unprefixed (Dutch) URL — e.g. a stale bookmark, a hardcoded link, or
 * a shared nl link — gets bounced to the English equivalent instead of silently being
 * switched back to Dutch. See App\Http\Middleware\Locale, which stashes the pre-request
 * locale onto the request for this middleware to read.
 *
 * Must run after SubstituteBindings so route-bound product/collection models are already
 * resolved (their English slug can differ from the Dutch one), and before TrackPageVisit so
 * the redirected-away Dutch URL isn't logged as a page visit.
 */
class RedirectToPreferredLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (
            ! $request->isMethod('GET')
            || App::getLocale() !== 'nl'
            || $request->attributes->get('locale.previous') !== 'en'
        ) {
            return $next($request);
        }

        $route = $request->route();
        $routeName = $route?->getName();

        if (! $routeName) {
            return $next($request);
        }

        $englishName = LocaleAwareUrlGenerator::englishRouteName($routeName);

        if (! RouteFacade::getRoutes()->hasNamedRoute($englishName)) {
            return $next($request);
        }

        $parameters = $route->originalParameters();

        foreach (['product', 'collection'] as $key) {
            if (! array_key_exists($key, $parameters)) {
                continue;
            }

            $model = $route->parameter($key);

            if (! $model instanceof Model) {
                continue;
            }

            $englishSlug = $model->localeUrl('en')->default()->value('slug')
                ?? $model->localeUrl('en')->value('slug');

            // No English translation exists for this element — nothing sensible to redirect
            // to, so let the Dutch page render rather than force a broken English URL.
            if (! $englishSlug) {
                return $next($request);
            }

            $parameters[$key] = $englishSlug;
        }

        // Locale::handle() already wrote session('locale') = 'nl' for this request (it's
        // URL-driven and runs before model binding, so it can't know a redirect is coming).
        // Restore it here so the session stays consistent with where the visitor is actually
        // being sent, in case anything reads it before the browser's follow-up request lands
        // (e.g. a Livewire AJAX call racing this redirect).
        session(['locale' => 'en']);

        $url = route($englishName, $parameters);
        $queryString = $request->getQueryString();

        return redirect($queryString ? "{$url}?{$queryString}" : $url);
    }
}

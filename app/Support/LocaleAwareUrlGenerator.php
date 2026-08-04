<?php

namespace App\Support;

use Illuminate\Routing\UrlGenerator;

/**
 * Extends (not wraps) Laravel's UrlGenerator so that every existing route($name, ...) call
 * in the app keeps working unchanged, while transparently resolving to the English route
 * variant (named "{name}.en") when the current locale is English. This must be a genuine
 * subclass rather than a decorator implementing the interface: Illuminate\Routing\Redirector
 * type-hints the concrete UrlGenerator class, so swapping in a plain interface
 * implementation for the "url" binding breaks redirect()/back() app-wide.
 *
 * This exists because routes/sytatsu.php registers each page twice: once at the root
 * (Dutch, the default) and once under /en (English, suffixed route names) — Laravel's
 * router does not cleanly support a single route with an optional {locale?} prefix
 * segment followed by additional required segments (confirmed: it fails to match the
 * "no locale" case for anything but a bare "/" route).
 */
class LocaleAwareUrlGenerator extends UrlGenerator
{
    public static function englishRouteName(string $name): string
    {
        return str_ends_with($name, '.en') ? $name : "{$name}.en";
    }

    public static function baseRouteName(string $name): string
    {
        return preg_replace('/\.en$/', '', $name);
    }

    protected function resolveName(string $name): string
    {
        if (app()->getLocale() === 'en') {
            $englishName = self::englishRouteName($name);

            if ($this->routes->hasNamedRoute($englishName)) {
                return $englishName;
            }
        }

        return $name;
    }

    public function route($name, $parameters = [], $absolute = true)
    {
        return parent::route($this->resolveName($name), $parameters, $absolute);
    }

    public function signedRoute($name, $parameters = [], $expiration = null, $absolute = true)
    {
        return parent::signedRoute($this->resolveName($name), $parameters, $expiration, $absolute);
    }

    public function temporarySignedRoute($name, $expiration, $parameters = [], $absolute = true)
    {
        return parent::temporarySignedRoute($this->resolveName($name), $expiration, $parameters, $absolute);
    }

    /**
     * The nl/en URL for the current request's route, keyed by locale — used to render
     * hreflang alternate tags. Returns an empty array for unnamed routes (e.g. the 404
     * fallback route), since there's nothing sensible to link to per locale for those.
     *
     * @return array<string, string>
     */
    public static function alternatesForCurrentRoute(): array
    {
        $route = request()?->route();

        if (! $route || ! $route->getName()) {
            return [];
        }

        $baseName = self::baseRouteName($route->getName());
        $previousLocale = app()->getLocale();
        $alternates = [];

        foreach (['nl', 'en'] as $locale) {
            $name = $locale === 'en' ? self::englishRouteName($baseName) : $baseName;

            if (! app('router')->getRoutes()->hasNamedRoute($name)) {
                continue;
            }

            app()->setLocale($locale);
            $alternates[$locale] = route($name, $route->originalParameters());
        }

        app()->setLocale($previousLocale);

        return $alternates;
    }
}

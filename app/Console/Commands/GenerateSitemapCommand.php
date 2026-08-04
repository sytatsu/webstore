<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Lunar\Models\Collection;
use Lunar\Models\Product;
use Lunar\Models\Url;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'webstore:generate-sitemap';

    protected $description = 'Generate public/sitemap.xml from static pages, products and collections, in both locales';

    /**
     * The locale-switcher config (used to render the language picker) is the source of
     * truth for which locales the storefront supports — see App\Http\Livewire\Sytatsu\
     * Components\LocaleSwitcher::getLocales(), which reads the same config key.
     *
     * @return array<int, string>
     */
    protected function locales(): array
    {
        return collect(config('locale-switcher.sytatsu', []))->pluck('locale')->all();
    }

    public function handle(): int
    {
        $routes = collect([
            ['route' => 'sytatsu.webstore.welcome', 'params' => [], 'lastmod' => now()],
            ['route' => 'sytatsu.about', 'params' => [], 'lastmod' => now()],
            ['route' => 'sytatsu.contact', 'params' => [], 'lastmod' => now()],
            ['route' => 'sytatsu.custom-print', 'params' => [], 'lastmod' => now()],
            ['route' => 'sytatsu.maintenance-repair', 'params' => [], 'lastmod' => now()],
            ['route' => 'sytatsu.webstore.collections', 'params' => [], 'lastmod' => now()],
        ]);

        $routes = $routes
            ->merge($this->urlEntries(Product::class, 'sytatsu.webstore.product', 'product'))
            ->merge($this->urlEntries(Collection::class, 'sytatsu.webstore.collection', 'collection'));

        $entries = $this->expandToLocales($routes);

        $this->writeSitemap($entries);

        $this->info("Sitemap generated with {$entries->count()} URLs at " . public_path('sitemap.xml'));

        return self::SUCCESS;
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{route: string, params: array, lastmod: Carbon}>
     */
    protected function urlEntries(string $modelClass, string $routeName, string $parameterKey): \Illuminate\Support\Collection
    {
        return Url::query()
            ->where('default', true)
            ->whereIn('element_type', [
                (new $modelClass)->getMorphClass(),
                $modelClass,
            ])
            // lastmod should reflect when the product/collection's own content last changed,
            // not when this Url row was last touched (slugs rarely change, so that would
            // almost never update).
            ->with('element:id,updated_at')
            ->get(['id', 'slug', 'element_type', 'element_id'])
            ->map(fn (Url $url) => [
                'route' => $routeName,
                'params' => [$parameterKey => $url->slug],
                'lastmod' => $url->element?->updated_at ?? now(),
            ]);
    }

    /**
     * For every route, generate one sitemap entry per locale (nl at root, en under /en/),
     * with hreflang alternates pointing at every other locale's URL for the same route.
     * Locale is resolved by App\Support\LocaleAwareUrlGenerator based on app()->getLocale()
     * (routes are registered twice, under ".en"-suffixed names for English — see
     * routes/sytatsu.php), so we toggle the app locale rather than pass a route parameter.
     *
     * @return \Illuminate\Support\Collection<int, array{loc: string, lastmod: Carbon, alternates: array<string,string>}>
     */
    protected function expandToLocales(\Illuminate\Support\Collection $routes): \Illuminate\Support\Collection
    {
        $locales = $this->locales();
        $previousLocale = app()->getLocale();

        $expanded = $routes->flatMap(function (array $entry) use ($locales) {
            $alternates = collect($locales)->mapWithKeys(function (string $locale) use ($entry) {
                app()->setLocale($locale);

                return [$locale => route($entry['route'], $entry['params'])];
            });

            return collect($locales)->map(fn (string $locale) => [
                'loc' => $alternates[$locale],
                'lastmod' => $entry['lastmod'],
                'alternates' => $alternates->all(),
            ]);
        });

        app()->setLocale($previousLocale);

        return $expanded;
    }

    protected function writeSitemap(\Illuminate\Support\Collection $entries): void
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $urlset = $document->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
        $document->appendChild($urlset);

        foreach ($entries as $entry) {
            $url = $document->createElement('url');
            $url->appendChild($document->createElement('loc', $entry['loc']));
            $url->appendChild($document->createElement('lastmod', $entry['lastmod']->toAtomString()));

            foreach ($entry['alternates'] as $hreflang => $href) {
                $link = $document->createElement('xhtml:link');
                $link->setAttribute('rel', 'alternate');
                $link->setAttribute('hreflang', $hreflang);
                $link->setAttribute('href', $href);
                $url->appendChild($link);
            }

            $urlset->appendChild($url);
        }

        $document->save(public_path('sitemap.xml'));
    }
}

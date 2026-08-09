<?php

namespace App\Providers;

use App\Filament\Extensions\OrderHeaderActionsExtension;
use App\Filament\Extensions\OrderItemsTableExtension;
use App\Filament\Extensions\OrderShippingInfolistExtension;
use App\Filament\Pages\BarBuilderDefaultArrangementPage;
use App\Filament\Pages\BarBuilderSettingsPage;
use App\Filament\Pages\HomepageHeroSettingsPage;
use App\Modifiers\DHLShippingModifier;
use App\Modifiers\PostNLShippingModifier;
use App\Scopes\PublishedProductScope;
use App\Support\LocaleAwareUrlGenerator;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Filament\Resources\OrderResource\Pages\Components\OrderItemsTable;
use Lunar\Admin\Filament\Resources\OrderResource\Pages\ManageOrder;
use Lunar\Admin\Support\Facades\LunarPanel;
use Sytatsu\PageVisits\Filament\Resources\PageVisitResource;
use Sytatsu\PageVisits\Filament\Resources\VisitorResource;
use Sytatsu\FilamentIssueTracker\Filament\Resources\ProjectResource;
use Sytatsu\FilamentIssueTracker\Filament\Resources\TicketResource;
use Sytatsu\FilamentIssueTracker\Filament\Pages\TicketSwimlanePage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Lunar\Base\ShippingModifiers;
use Lunar\Models\Collection;
use Lunar\Models\Order;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\Url as LunarUrl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Replaces Laravel's "url" singleton with a subclass so route($name, ...) resolves
        // to the ".en" route variant when the current locale is English (see
        // App\Support\LocaleAwareUrlGenerator). Mirrors RoutingServiceProvider::
        // registerUrlGenerator() exactly, since it must run again for the new class.
        $this->app->singleton('url', function ($app) {
            $routes = $app['router']->getRoutes();
            $app->instance('routes', $routes);

            $url = new LocaleAwareUrlGenerator(
                $routes,
                $app->rebinding('request', function ($app, $request) {
                    $app['url']->setRequest($request);
                }),
                $app['config']['app.asset_url']
            );

            $url->setSessionResolver(function () use ($app) {
                return $app['session'] ?? null;
            });

            $url->setKeyResolver(function () use ($app) {
                $config = $app->make('config');

                return [$config->get('app.key'), ...($config->get('app.previous_keys') ?? [])];
            });

            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });

        LunarPanel::panel(fn ($panel) => $panel
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->resources([
                PageVisitResource::class,
                VisitorResource::class,
                ProjectResource::class,
                TicketResource::class,
            ])
            ->pages([
                TicketSwimlanePage::class,
                BarBuilderDefaultArrangementPage::class,
                BarBuilderSettingsPage::class,
                HomepageHeroSettingsPage::class,
            ])
        );
        LunarPanel::extensions([
            OrderItemsTable::class => [
                OrderItemsTableExtension::class,
            ],
            ManageOrder::class => [
                OrderHeaderActionsExtension::class,
                OrderShippingInfolistExtension::class,
            ],
        ]);
        LunarPanel::register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ShippingModifiers $shippingModifiers): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $shippingModifiers->add(PostNLShippingModifier::class);
        $shippingModifiers->add(DHLShippingModifier::class);

        Order::observe(\App\Observers\OrderObserver::class);
        ProductVariant::observe(\App\Observers\ProductVariantObserver::class);

        Product::addGlobalScope(new PublishedProductScope);

        // Registered inside the `booted` event, not directly here, so this runs after every
        // provider's boot() has finished — including Lunar's ModelManifest, which registers
        // its own plain-Eloquent 'product'/'collection' binders (lookup by id) from its own
        // provider. That guarantees these slug-aware overrides win regardless of provider
        // boot order. Also deliberately NOT in routes/sytatsu.php: Laravel skips
        // re-executing routes/*.php whenever `route:cache` is active (loads the compiled
        // route table instead), so a Route::model() override placed there would silently stop
        // taking effect on any cached-routes deploy, leaving Lunar's id-based default active
        // and 404-ing every product/collection page.
        $this->app->booted(function () {
            Route::model('product', Product::class, function (string $slug) {
                $url = LunarUrl::query()
                    ->where('slug', $slug)
                    ->whereIn('element_type', [
                        (new Product)->getMorphClass(),
                        Product::class,
                        'product',
                    ])
                    ->orderBy('default', 'desc')
                    ->orderBy('id', 'desc')
                    ->firstOrFail();

                $this->redirectToDefaultUrlIfStale($url, 'sytatsu.webstore.product', 'product');

                return $url->element;
            });

            Route::model('collection', Collection::class, function (string $slug) {
                $url = LunarUrl::query()
                    ->where('slug', $slug)
                    ->whereIn('element_type', [
                        (new Collection)->getMorphClass(),
                        Collection::class,
                        'collection',
                    ])
                    ->orderBy('default', 'desc')
                    ->orderBy('id', 'desc')
                    ->firstOrFail();

                $this->redirectToDefaultUrlIfStale($url, 'sytatsu.webstore.collection', 'collection');

                return $url->element;
            });
        });
    }

    /**
     * When a product/collection is resolved via a historical (non-default) slug, 301 redirect
     * to the current canonical URL so search engines consolidate link equity onto one URL.
     */
    private function redirectToDefaultUrlIfStale(LunarUrl $url, string $routeName, string $parameterKey): void
    {
        if ($url->default) {
            return;
        }

        $defaultSlug = LunarUrl::query()
            ->where('element_type', $url->element_type)
            ->where('element_id', $url->element_id)
            ->where('default', true)
            ->value('slug');

        if (! $defaultSlug || $defaultSlug === $url->slug) {
            return;
        }

        $redirectUrl = route($routeName, [$parameterKey => $defaultSlug]);
        $queryString = request()->getQueryString();

        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            redirect($queryString ? "{$redirectUrl}?{$queryString}" : $redirectUrl, 301)
        );
    }
}

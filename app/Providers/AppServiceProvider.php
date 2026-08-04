<?php

namespace App\Providers;

use App\Filament\Extensions\OrderItemsTableExtension;
use App\Filament\Pages\BarBuilderDefaultArrangementPage;
use App\Filament\Pages\BarBuilderSettingsPage;
use App\Filament\Pages\HomepageHeroSettingsPage;
use App\Modifiers\DHLShippingModifier;
use App\Modifiers\PostNLShippingModifier;
use App\Scopes\PublishedProductScope;
use App\Support\LocaleAwareUrlGenerator;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Filament\Resources\OrderResource\Pages\Components\OrderItemsTable;
use Lunar\Admin\Support\Facades\LunarPanel;
use Sytatsu\PageVisits\Filament\Resources\PageVisitResource;
use Sytatsu\PageVisits\Filament\Resources\VisitorResource;
use Sytatsu\FilamentIssueTracker\Filament\Resources\ProjectResource;
use Sytatsu\FilamentIssueTracker\Filament\Resources\TicketResource;
use Sytatsu\FilamentIssueTracker\Filament\Pages\TicketSwimlanePage;
use Illuminate\Support\Facades\URL;
use Lunar\Base\ShippingModifiers;
use Lunar\Models\Order;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

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

//        $shippingModifiers->add(PostNLShippingModifier::class);
        $shippingModifiers->add(DHLShippingModifier::class);

        Order::observe(\App\Observers\OrderObserver::class);
        ProductVariant::observe(\App\Observers\ProductVariantObserver::class);

        Product::addGlobalScope(new PublishedProductScope);
    }
}

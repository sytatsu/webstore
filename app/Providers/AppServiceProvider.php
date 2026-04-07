<?php

namespace App\Providers;

use App\Modifiers\PostNLShippingModifier;
use App\Scopes\PublishedProductScope;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
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
        LunarPanel::panel(fn ($panel) => $panel
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
        );
        LunarPanel::register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ShippingModifiers $shippingModifiers): void
    {
        $shippingModifiers->add(PostNLShippingModifier::class);

        Order::observe(\App\Observers\OrderObserver::class);
        ProductVariant::observe(\App\Observers\ProductVariantObserver::class);

        Product::addGlobalScope(new PublishedProductScope);
    }
}

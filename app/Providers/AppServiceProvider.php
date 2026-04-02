<?php

namespace App\Providers;

use App\Modifiers\PostNLShippingModifier;
use App\Scopes\PublishedProductScope;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Base\ShippingModifiers;
use Lunar\Models\Order;
use Lunar\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        LunarPanel::register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ShippingModifiers $shippingModifiers): void
    {
        $shippingModifiers->add(PostNLShippingModifier::class);

        Order::observe(\App\Observers\OrderObserver::class);

        Product::addGlobalScope(new PublishedProductScope);
    }
}

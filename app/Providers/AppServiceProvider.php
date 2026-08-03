<?php

namespace App\Providers;

use App\Filament\Extensions\OrderItemsTableExtension;
use App\Filament\Pages\BarBuilderDefaultArrangementPage;
use App\Filament\Pages\BarBuilderSettingsPage;
use App\Modifiers\DHLShippingModifier;
use App\Modifiers\PostNLShippingModifier;
use App\Scopes\PublishedProductScope;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Filament\Resources\OrderResource\Pages\Components\OrderItemsTable;
use Lunar\Admin\Support\Facades\LunarPanel;
use Sytatsu\PageVisits\Filament\Resources\PageVisitResource;
use Sytatsu\PageVisits\Filament\Resources\VisitorResource;
use Sytatsu\FilamentIssueTracker\Filament\Resources\ProjectResource;
use Sytatsu\FilamentIssueTracker\Filament\Resources\TicketResource;
use Sytatsu\FilamentIssueTracker\Filament\Pages\TicketSwimlanePage;
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
//        $shippingModifiers->add(PostNLShippingModifier::class);
        $shippingModifiers->add(DHLShippingModifier::class);

        Order::observe(\App\Observers\OrderObserver::class);
        ProductVariant::observe(\App\Observers\ProductVariantObserver::class);

        Product::addGlobalScope(new PublishedProductScope);
    }
}

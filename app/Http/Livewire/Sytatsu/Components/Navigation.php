<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Filament\Pages\BarBuilderSettingsPage;
use App\Filament\Pages\NavigationSettingsPage;
use App\Models\WebstoreSetting;
use App\Services\StorefrontService;
use Illuminate\Support\Collection;
use Livewire\Component;

class Navigation extends Component
{
    public function render(StorefrontService $storefrontService)
    {
        $groupHandles = WebstoreSetting::getByKey('navigation_collection_groups', ['printed']);

        // whereIn('handle', $groupHandles) (inside getCollectionTreeByGroupHandles)
        // only filters, it doesn't honor the array's order — so group by
        // handle first, then walk $groupHandles itself to put the groups
        // back in the order the admin actually chose
        // (NavigationSettingsPage). Without this, reordering the groups
        // there had no visible effect on the dropdown at all.
        $collectionsByGroupHandle = $storefrontService->getCollectionTreeByGroupHandles($groupHandles)
            ->groupBy(fn ($collection) => $collection->group->handle);

        $collections = collect($groupHandles)
            ->map(fn ($handle) => $collectionsByGroupHandle->get($handle))
            ->filter()
            ->values();

        $fdmPrintingSlugs = WebstoreSetting::getByKey('navigation_fdm_printing_handles', ['polymaker']);

        // Same ordering problem as above: getCollectionsBySlugs() only
        // filters by whereIn, so re-sort by $fdmPrintingSlugs ourselves.
        $fdmPrintingCollectionsBySlug = $storefrontService->getCollectionsBySlugs($fdmPrintingSlugs)
            ->keyBy(fn ($collection) => $collection->defaultUrl?->slug);

        $fdmPrintingCollections = collect($fdmPrintingSlugs)
            ->map(fn ($slug) => $fdmPrintingCollectionsBySlug->get($slug))
            ->filter()
            ->values();

        // "Which top-level items, in what order" — gated per-type (Clickerz
        // on the Bar Builder being enabled, FDM Printing on actually having
        // collections) rather than baked into the stored order itself, so
        // enabling the Bar Builder later doesn't require re-saving
        // NavigationSettingsPage just to make a previously-saved "clickerz"
        // entry start rendering.
        $topLevelOrder = NavigationSettingsPage::normalizedTopLevelOrder(
            WebstoreSetting::getByKey(NavigationSettingsPage::TOP_LEVEL_ORDER_KEY, NavigationSettingsPage::DEFAULT_TOP_LEVEL_ORDER)
        )->filter(fn ($type) => match ($type) {
            'clickerz' => BarBuilderSettingsPage::isEnabled(),
            'fdm_printing' => $fdmPrintingCollections->isNotEmpty(),
            default => true,
        });

        return view('sytatsu.components.navigation', [
            'collections' => $collections,
            'fdmPrintingCollections' => $fdmPrintingCollections,
            'topLevelOrder' => $topLevelOrder,
        ]);
    }
}

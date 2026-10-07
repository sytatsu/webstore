<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Filament\Pages\HomeFeaturedCollectionsSettingsPage;
use App\Models\WebstoreSetting;
use App\DTOs\ProductCollectionDTO;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\StorefrontService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Lunar\Base\Traits\HasTranslations;

class Welcome extends SytatsuBasePage
{
    use HasTranslations;

    protected string $view = 'sytatsu.webstore.welcome';
    protected ?string $title = 'Print & Shop';

    public ?string $label = null;

    protected StorefrontService $storefrontService;

    /** @var Collection $products */
    protected Collection $products;

    /** @var SupportCollection<ProductCollectionDTO> $collections */
    protected SupportCollection $collections;

    protected array $collectionIds = [];

    /**
     * The admin-configured order of homepage elements — a mix of
     * `{"type": "collection", "collection_id": ...}` and
     * `{"type": "clickerz"}` rows (HomeFeaturedCollectionsSettingsPage).
     * Kept separately from $collectionIds so the Clickerz Bar CTA's
     * position among the featured collections survives into the view
     * instead of always rendering in one fixed spot.
     *
     * @var array<int, array<string, mixed>>
     */
    protected array $elements = [];

    public string $gridColumns = 'grid-cols-2 md:grid-cols-4 ';
    public string $maxWidth = 'max-w-[85rem]';

    public function mount(StorefrontService $storefrontService): void {
        $this->storefrontService = $storefrontService;

        $stored = WebstoreSetting::getByKey('home_featured_collections', []);
        $this->elements = HomeFeaturedCollectionsSettingsPage::normalizedElements($stored)->all();

        $this->collectionIds = collect($this->elements)
            ->where('type', HomeFeaturedCollectionsSettingsPage::TYPE_COLLECTION)
            ->pluck('collection_id')
            ->all();
    }

    /**
     * Helper to translate an array of locales.
     */
    protected function translateArray($values, ?string $locale = null): ?string
    {
        if (!$values) {
            return null;
        }

        // If it's already a string, return it (handles incorrectly stored data)
        if (is_string($values)) {
            return $values;
        }

        if (!is_array($values)) {
            return (string) $values;
        }

        $locale = $locale ?: app()->getLocale();

        return $values[$locale] ?? $values[config('app.fallback_locale', 'en')] ?? collect($values)->first();
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
    {
        $this->setViewAttributes([
            'homepageElements' => $this->getHomepageElementsAttribute(),
            'gridColumns' => 'grid-cols-2 lg:grid-cols-4',
            'maxWidth' => $this->maxWidth,
            'showFilters' => false,
        ]);

        return parent::render();
    }

    public function getCollectionsAttribute(): SupportCollection
    {
        if (!isset($this->collections)) {
            if (empty($this->collectionIds)) {
                $this->collections = collect();
            } else {
                $this->collections = $this->storefrontService->getCollectionsAndDescendantsWithLimitedProducts($this->collectionIds);
            }
        }

        return $this->collections;
    }

    /**
     * $this->elements in admin-chosen order, with each `collection` row
     * resolved to its ProductCollectionDTO (dropped if the collection no
     * longer exists) and each `clickerz` row kept as a bare marker —
     * welcome.blade.php renders the CTA for the latter and delegates to
     * collection-cards.blade.php for the former, one collection at a
     * time so the Clickerz CTA can sit between any two of them.
     *
     * @return SupportCollection<int, array{type: string, dto?: ProductCollectionDTO}>
     */
    public function getHomepageElementsAttribute(): SupportCollection
    {
        $dtosByCollectionId = $this->getCollectionsAttribute()->keyBy(fn (ProductCollectionDTO $dto) => $dto->collection->id);

        // A Clickerz row is dropped here rather than in welcome.blade.php
        // so both gates — Bar Builder disabled, and the hero already
        // being the Clickerz hero (two Clickerz promos stacked directly
        // on top of each other would be redundant) — live in one place
        // next to the rest of the list-building logic.
        $clickerzAllowed = \App\Filament\Pages\BarBuilderSettingsPage::isEnabled()
            && \App\Filament\Pages\HomepageHeroSettingsPage::current() !== 'clickerz';

        return collect($this->elements)
            ->map(function (array $element) use ($dtosByCollectionId, $clickerzAllowed) {
                if ($element['type'] === HomeFeaturedCollectionsSettingsPage::TYPE_CLICKERZ) {
                    return $clickerzAllowed ? ['type' => HomeFeaturedCollectionsSettingsPage::TYPE_CLICKERZ] : null;
                }

                $dto = $dtosByCollectionId->get($element['collection_id']);

                return $dto ? ['type' => HomeFeaturedCollectionsSettingsPage::TYPE_COLLECTION, 'dto' => $dto] : null;
            })
            ->filter()
            ->values();
    }
}

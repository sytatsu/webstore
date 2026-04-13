<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

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

    public string $gridColumns = 'grid-cols-2 md:grid-cols-4 ';
    public string $maxWidth = 'max-w-[85rem]';

    public function mount(StorefrontService $storefrontService): void {
        $this->storefrontService = $storefrontService;

        $this->collectionIds = WebstoreSetting::getByKey('home_featured_collections', []);

        // Ensure collectionIds is an array
        if (!is_array($this->collectionIds)) {
            $this->collectionIds = [];
        }
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
            'collections' => $this->getCollectionsAttribute(),
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
}

<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Models\HomeSettings;
use App\DTOs\ProductCollectionDTO;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\StorefrontService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class Welcome extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.welcome';
    protected ?string $title = 'Print & Shop';

    public ?string $label = null;
    public ?string $homeTitle = null;
    public ?string $homeSubTitle = null;

    protected StorefrontService $storefrontService;

    /** @var Collection $products */
    protected Collection $products;

    /** @var SupportCollection<ProductCollectionDTO> $collections */
    protected SupportCollection $collections;

    protected array $collectionIds = [];

    public string $gridColumns = 'grid-cols-2 lg:grid-cols-4';
    public string $maxWidth = 'max-w-[85rem]';

    public function mount(StorefrontService $storefrontService): void {
        $this->storefrontService = $storefrontService;

        $settings = HomeSettings::with(['homeCollections.collection'])->where('is_active', true)->first();
        if ($settings) {
            $this->collectionIds = $settings->homeCollections->pluck('collection_id')->toArray();
            if ($settings->title) {
                $this->homeTitle = $settings->title;
                $this->setTitle($settings->title);
            }
            $this->homeSubTitle = $settings->sub_title;
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
    {
        $this->setViewAttributes([
            'collections' => $this->getCollectionsAttribute(),
            'gridColumns' => 'grid-cols-2 lg:grid-cols-4',
            'maxWidth' => $this->maxWidth,
            'showFilters' => false,
            'homeTitle' => $this->homeTitle,
            'homeSubTitle' => $this->homeSubTitle,
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

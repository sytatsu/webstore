<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\StorefrontService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Lunar\Models\Collection;

class CollectionPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.collection';
    public ?string $label;

    /** @var EloquentCollection $products */
    public EloquentCollection $products;

    public Collection $collection;
    public string $maxWidth = 'max-w-[85rem]';

    public array $subCollections = [];
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
    public bool $inStock = false;

    protected $queryString = [
        'subCollections' => ['except' => []],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'inStock' => ['except' => false],
    ];

    public array $filters = [];

    protected StorefrontService $storefrontService;

    protected $listeners = ['filtersUpdated' => 'updateFilters'];

    public function updated($name): void
    {
        if (in_array($name, ['subCollections', 'minPrice', 'maxPrice', 'inStock'])) {
            $this->syncFilters();
        }
    }

    public function updateFilters(array $filters): void
    {
        $this->subCollections = $filters['subCollections'] ?? [];
        $this->minPrice = $filters['minPrice'] ?? null;
        $this->maxPrice = $filters['maxPrice'] ?? null;
        $this->inStock = $filters['inStock'] ?? false;

        $this->syncFilters();
    }

    protected function syncFilters(): void
    {
        $this->filters = [
            'subCollections' => $this->subCollections,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'inStock' => $this->inStock,
        ];

        unset($this->products);
        $this->getProductsAttribute();
    }

    public function mount(Collection $collection, StorefrontService $storefrontService): void
    {
        $this->collection = $collection;
        $this->storefrontService = $storefrontService;
        $this->setTitle($collection->translateAttribute('name'));
        $this->label = sprintf('%s: %s', __('Collection'), $collection->translateAttribute('name'));

        $this->syncFilters();
    }

    public function getProductsAttribute(): EloquentCollection
    {
        if (isset($this->products)) {
            return $this->products;
        }

        $collectionIds = collect([$this->collection->id]);

        $storefrontService = app(StorefrontService::class);

        if (!empty($this->filters['subCollections'])) {
            $collectionIds = collect($this->filters['subCollections']);
        } else {
            $collections = $storefrontService->getCollectionAndDescendants($this->collection);
            $collectionIds = $collections->pluck('id');
        }

        // Get all products from given collections with filters
        $this->products = $storefrontService->getProductsForCollections($collectionIds, null, $this->filters);

        return $this->products;
    }

    public function resetFilters(): void
    {
        $this->subCollections = [];
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->inStock = false;

        $this->syncFilters();

        $this->dispatch('filtersReset');
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
    {
        $showFilterCategories = $this->collection->translateAttribute('filter_categories');
        $showFilterPrice = $this->collection->translateAttribute('filter_price');
        $showFilterAvailability = $this->collection->translateAttribute('filter_availability');

        $showFilters = $showFilterCategories || $showFilterPrice || $showFilterAvailability;

        $this->setViewAttributes([
            'products' => $this->getProductsAttribute(),
            'gridColumns' => $showFilters ? 'grid-cols-2 lg:grid-cols-3' : 'grid-cols-2 lg:grid-cols-4',
            'maxWidth' => $this->maxWidth,
            'showFilters' => $showFilters,
            'showFilterCategories' => $showFilterCategories,
            'showFilterPrice' => $showFilterPrice,
            'showFilterAvailability' => $showFilterAvailability,
        ]);

        return parent::render();
    }
}

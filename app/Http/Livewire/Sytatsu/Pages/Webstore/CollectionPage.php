<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\StorefrontService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Lunar\Models\Collection;

class CollectionPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.collection-pages.default';

    private const VIEW_BASE = 'sytatsu.webstore.collection-pages.';
    public ?string $label;

    /** @var EloquentCollection $products */
    public EloquentCollection $products;

    public Collection $collection;
    public string $maxWidth = 'max-w-[85rem]';

    public array $subCollections = [];
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
    public bool $inStock = false;
    public ?string $sort = null;

    protected $queryString = [
        'subCollections' => ['except' => []],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'inStock' => ['except' => false],
        'sort' => ['except' => null],
    ];

    public array $filters = [];

    protected StorefrontService $storefrontService;

    protected $listeners = ['filtersUpdated' => 'updateFilters'];

    public function updated($name): void
    {
        if (in_array($name, ['subCollections', 'minPrice', 'maxPrice', 'inStock', 'sort'])) {
            $this->syncFilters();
        }
    }

    public function updateFilters(array $filters): void
    {
        $this->subCollections = $filters['subCollections'] ?? [];
        $this->minPrice = $filters['minPrice'] ?? null;
        $this->maxPrice = $filters['maxPrice'] ?? null;
        $this->inStock = $filters['inStock'] ?? false;
        $this->sort = $filters['sort'] ?? $this->collection->translateAttribute('default_sort');

        $this->syncFilters();
    }

    protected function syncFilters(): void
    {
        $this->filters = [
            'subCollections' => $this->subCollections,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'inStock' => $this->inStock,
            'sort' => $this->sort,
        ];

        $this->getProductsAttribute(resetProducts: true);
    }

    public function mount(Collection $collection, StorefrontService $storefrontService): void
    {
        $this->collection = $collection;
        $this->storefrontService = $storefrontService;

        $collectionView = $collection->translateAttribute('collection_view') ?: 'default';
        $this->view = self::VIEW_BASE . $collectionView;

        $this->setTitle($collection->translateAttribute('name'));
        $this->label = sprintf('%s: %s', __('Collection'), $collection->translateAttribute('name'));

        if (!$this->sort) {
            $this->sort = $this->collection->translateAttribute('default_sort');
        }

        $this->syncFilters();
    }

    public function getProductsAttribute(bool $resetProducts = false): EloquentCollection
    {
        if (!isset($this->products) && !$resetProducts) {
            return $this->products;
        }

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
        $this->sort = $this->collection->translateAttribute('default_sort');

        $this->syncFilters();

        $this->dispatch('filtersReset');
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
    {
        $showFilterCategories = $this->collection->translateAttribute('filter_categories');
        $showFilterPrice = $this->collection->translateAttribute('filter_price');
        $showFilterAvailability = $this->collection->translateAttribute('filter_availability');
        $showSorting = $this->collection->translateAttribute('show_sorting');

        $showFilters = $showFilterCategories || $showFilterPrice || $showFilterAvailability || $showSorting;

        $this->setViewAttributes([
            'products' => $this->getProductsAttribute(),
            'gridColumns' => $showFilters ? 'grid-cols-2 xl:grid-cols-3' : 'grid-cols-2 lg:grid-cols-4',
            'maxWidth' => $this->maxWidth,
            'showFilters' => $showFilters,
            'showFilterCategories' => $showFilterCategories,
            'showFilterPrice' => $showFilterPrice,
            'showFilterAvailability' => $showFilterAvailability,
            'showSorting' => $showSorting,
        ]);

        return parent::render();
    }
}

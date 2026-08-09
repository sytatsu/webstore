<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\StorefrontService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Lunar\Models\Collection;

class CollectionPage extends SytatsuBasePage
{
    use WithPagination;

    protected string $view = 'sytatsu.webstore.collection';
    public ?string $label;

    public Collection $collection;
    public string $maxWidth = 'max-w-[85rem]';

    protected int $perPage = 24;

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

    protected $listeners = ['filtersUpdated' => 'updateFilters'];

    public function updated($name): void
    {
        if (in_array($name, ['subCollections', 'minPrice', 'maxPrice', 'inStock', 'sort'])) {
            $this->resetPage();
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

        $this->resetPage();
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
    }

    public function mount(Collection $collection, StorefrontService $storefrontService): void
    {
        $this->collection = $collection;
        $this->setTitle($collection->translateAttribute('name'));
        $this->label = sprintf('%s: %s', __('Collection'), $collection->translateAttribute('name'));

        $collectionDescription = strip_tags($collection->translateAttribute('description') ?: '');
        $this->setDescription($collectionDescription
            ? Str::limit($collectionDescription, 160)
            : sprintf(__('Shop the %s collection at Sytatsu.'), $collection->translateAttribute('name')));
        $this->setImage($collection->getThumbnailImage());

        if (!$this->sort) {
            $this->sort = $this->collection->translateAttribute('default_sort');
        }

        $this->syncFilters();
    }

    public function getProducts(): LengthAwarePaginator
    {
        $storefrontService = app(StorefrontService::class);

        if (!empty($this->filters['subCollections'])) {
            $collectionIds = collect($this->filters['subCollections']);
        } else {
            $collections = $storefrontService->getCollectionAndDescendants($this->collection);
            $collectionIds = $collections->pluck('id');
        }

        return $storefrontService->paginateProductsForCollections($collectionIds, $this->perPage, $this->filters);
    }

    public function resetFilters(): void
    {
        $this->subCollections = [];
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->inStock = false;
        $this->sort = $this->collection->translateAttribute('default_sort');

        $this->resetPage();
        $this->syncFilters();

        $this->dispatch('filtersReset');
    }

    public function paginationView(): string
    {
        return 'vendor.pagination.sytatsu';
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
    {
        $showFilterCategories = $this->collection->translateAttribute('filter_categories');
        $showFilterPrice = $this->collection->translateAttribute('filter_price');
        $showFilterAvailability = $this->collection->translateAttribute('filter_availability');
        $showSorting = $this->collection->translateAttribute('show_sorting');

        $showFilters = $showFilterCategories || $showFilterPrice || $showFilterAvailability || $showSorting;

        $this->setViewAttributes([
            'products' => $this->getProducts(),
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

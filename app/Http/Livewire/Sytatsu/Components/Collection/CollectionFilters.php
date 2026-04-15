<?php

namespace App\Http\Livewire\Sytatsu\Components\Collection;

use App\Services\StorefrontService;
use Livewire\Component;
use Lunar\Models\Collection;

class CollectionFilters extends Component
{
    public Collection $collection;
    public array $selectedSubCollections = [];
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
    public bool $inStockOnly = false;
    public ?string $selectedSort = null;
    public array $initialFilters = [];

    public bool $showCategories = true;
    public bool $showPrice = true;
    public bool $showAvailability = true;
    public bool $showSorting = false;
    public bool $isFiltersExpanded = false;
    public bool $isSortingExpanded = false;

    public function mount(Collection $collection, array $initialFilters = [], bool $showCategories = true, bool $showPrice = true, bool $showAvailability = true, bool $showSorting = false): void
    {
        $this->collection = $collection;
        $this->initialFilters = $initialFilters;
        $this->showCategories = $showCategories;
        $this->showPrice = $showPrice;
        $this->showAvailability = $showAvailability;
        $this->showSorting = $showSorting;

        $this->syncFromInitial();
    }

    public function toggleFilters(): void
    {
        $this->isFiltersExpanded = !$this->isFiltersExpanded;
    }

    public function toggleSorting(): void
    {
        $this->isSortingExpanded = !$this->isSortingExpanded;
    }

    public function updatedInitialFilters(): void
    {
        $this->syncFromInitial();
    }

    protected function syncFromInitial(): void
    {
        if (!empty($this->initialFilters)) {
            $this->selectedSubCollections = $this->initialFilters['subCollections'] ?? [];
            $this->minPrice = $this->initialFilters['minPrice'] ?? null;
            $this->maxPrice = $this->initialFilters['maxPrice'] ?? null;
            $this->inStockOnly = $this->initialFilters['inStock'] ?? false;
            $this->selectedSort = $this->initialFilters['sort'] ?? null;
        }
    }

    public function updated($propertyName): void
    {
        $this->applyFilters();
    }

    public function applyFilters(): void
    {
        $this->dispatch('filtersUpdated', [
            'subCollections' => $this->selectedSubCollections,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'inStock' => $this->inStockOnly,
            'sort' => $this->selectedSort,
        ]);
    }

    protected $listeners = ['filtersReset' => 'resetFilters'];

    public function resetFilters(): void
    {
        $this->selectedSubCollections = [];
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->inStockOnly = false;
    }

    public function getHasFiltersProperty(): bool
    {
        return !empty($this->selectedSubCollections) ||
            $this->minPrice !== null ||
            $this->maxPrice !== null ||
            $this->inStockOnly;
    }

    public function getHasContentProperty(): bool
    {
        return ($this->showCategories && $this->collection->children()->count() > 0) ||
            $this->showPrice ||
            $this->showAvailability;

    }

    public function render()
    {
        return view('sytatsu.components.livewire.collection.collection-filters', [
            'subCollections' => $this->collection->children()->defaultOrder()->get(),
        ]);
    }
}

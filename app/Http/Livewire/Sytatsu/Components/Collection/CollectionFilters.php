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
    public array $initialFilters = [];

    public function mount(Collection $collection, array $initialFilters = []): void
    {
        $this->collection = $collection;
        $this->initialFilters = $initialFilters;

        $this->syncFromInitial();
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

    public function render()
    {
        return view('sytatsu.components.livewire.collection.collection-filters', [
            'subCollections' => $this->collection->children()->defaultOrder()->get(),
        ]);
    }
}

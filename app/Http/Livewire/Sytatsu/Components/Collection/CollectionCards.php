<?php

namespace App\Http\Livewire\Sytatsu\Components\Collection;

use App\DTOs\ProductCollectionDTO;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;

class CollectionCards extends Component
{
    /** @var Collection<ProductCollectionDTO>|ProductCollectionDTO $collections */
    public $collections;

    public bool $showMore = true;
    public string $gridColumns = 'grid-cols-2 lg:grid-cols-4';
    public string $maxWidth = 'max-w-[85rem]';

    public function mount($collections, bool $showMore = true, string $gridColumns = 'grid-cols-2 lg:grid-cols-4', string $maxWidth = 'max-w-[85rem]'): void
    {
        $this->collections = $collections;
        $this->showMore = $showMore;
        $this->gridColumns = $gridColumns;
        $this->maxWidth = $maxWidth;
    }

    public function getCollectionListProperty(): Collection
    {
        if ($this->collections instanceof ProductCollectionDTO) {
            return collect([$this->collections]);
        }

        return collect($this->collections);
    }

    public function getActiveCollectionsProperty(): Collection
    {
        return $this->getCollectionListProperty()->filter(fn (ProductCollectionDTO $col) => $col->products->isNotEmpty());
    }

    public function render(): Factory|View|Application
    {
        return view('sytatsu.components.livewire.collection.collection-cards');
    }
}

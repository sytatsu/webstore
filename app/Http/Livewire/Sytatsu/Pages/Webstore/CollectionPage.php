<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Lunar\Models\Collection;
use Lunar\Models\Product;

class CollectionPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.listing';
    public ?string $label;

    /** @var EloquentCollection<ProductPage> $products */
    public EloquentCollection $products;

    public Collection $collection;

    public function mount(Collection $collection): void
    {
        $this->collection = $collection;
        $this->setTitle($collection->translateAttribute('name'));
        $this->label = sprintf('%s: %s', __('Collection'), $collection->translateAttribute('name'));
    }

    /** @return EloquentCollection<ProductPage> */
    public function getProducts(): EloquentCollection
    {
        if (isset($this->products)) {
            return $this->products;
        }

        // Decendants
        $collections = $this->collection->descendants->toFlatTree();
        $collections->add($this->collection);

        // Get all products from given collections
        $this->products = Product::query()
            ->with('collections')
            ->whereRelation('collections', fn ($builder) => $builder->whereIn('lunar_collections.id', $collections->pluck('id')))
            ->get();

        return $this->products;
    }
}

<?php

namespace App\DTOs;

use App\Repositories\CollectionRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Wireable;
use Lunar\Models\Collection as LunarCollection;

class ProductCollectionDTO implements Wireable
{
    public function __construct(
        public readonly LunarCollection $collection,
        public readonly SupportCollection $products
    ) {
    }

    public function toLivewire()
    {
        return [
            'collection_id' => $this->collection->id,
            'product_ids' => $this->products->pluck('id')->toArray(),
        ];
    }

    public static function fromLivewire($value)
    {
        $collection = app(CollectionRepository::class)->find($value['collection_id']);
        $products = app(ProductRepository::class)->getByIds($value['product_ids']);

        return new static($collection, $products);
    }

    public function getName(): string
    {
        return $this->collection->translateAttribute('name');
    }
}

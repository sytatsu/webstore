<?php

namespace App\Http\Livewire\Sytatsu\Components\Product;

use App\DataTransformers\PriceTransformer;
use App\DataTransformers\ProductTransformer;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Models\Product;

class ProductTile extends Component
{
    public Product $product;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function getPriceRangeString(): string
    {
        return PriceTransformer::rangeString(priceCollection: $this->product->prices);
    }

    public function getProductOptionsArray(): array
    {
        return ProductTransformer::productOptionsArray(product: $this->product);
    }

    public function render(): Factory|View|Application
    {
        return view('sytatsu.components.livewire.product.product-tile', [
            'product' => $this->product
        ]);
    }
}

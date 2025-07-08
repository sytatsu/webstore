<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\DataTransformers\PriceTransformer;
use App\DataTransformers\ProductTransformer;
use Lunar\Models\Product;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;

class ProductPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.product';

    public Product $product;

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->setTitle($product->translateAttribute('name'));

        $this->setViewAttributes([
            'product' => $this->product,
        ]);
    }

    public function getPriceRangeString(): string
    {
        return PriceTransformer::rangeString(priceCollection: $this->product->prices);
    }

    public function getProductOptionsArray(): array
    {
        return ProductTransformer::productOptionsArray(product: $this->product);
    }
}

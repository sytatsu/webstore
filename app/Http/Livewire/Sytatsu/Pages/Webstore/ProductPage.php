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

    public array $selectedOptionValues = [];

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->setTitle($product->translateAttribute('name'));

        $this->setViewAttributes([
            'product' => $this->product,
        ]);

        $this->selectedOptionValues = $this->productOptions->mapWithKeys(function ($data) {
            return [$data['option']->id => $data['values']->first()->id];
        })->toArray();

        if (! $this->variant) {
            abort(404);
        }
    }

    /**
     * Computed property to get variant.
     *
     * @return \Lunar\Models\ProductVariant
     */
    public function getVariantProperty()
    {
        return $this->product->variants->first(function ($variant) {
            return ! $variant->values->pluck('id')
                ->diff(
                    collect($this->selectedOptionValues)->values()
                )->count();
        });
    }

    /**
     * Computed property to return all available option values.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProductOptionValuesProperty()
    {
        return $this->product->variants->pluck('values')->flatten();
    }

    /**
     * Computed propert to get available product options with values.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProductOptionsProperty()
    {
        return $this->productOptionValues->unique('id')->groupBy('product_option_id')
            ->map(function ($values) {
                return [
                    'option' => $values->first()->option,
                    'values' => $values,
                ];
            })->values();
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

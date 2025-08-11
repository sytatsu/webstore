<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Services\WebstoreHelperService;
use Livewire\Attributes\Url;
use Lunar\Models\Product;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductVariant;

class ProductPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.product';

    #[Url(except: '')]
    public string $purchasable_id = '';
    public Product $product;

    public array $selectedOptionValues = [];

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->setTitle($product->translateAttribute('name'));

        $this->setViewAttributes([
            'product' => $this->product,
        ]);

        if ($this->purchasable_id) {
            $optionValues = ProductVariant::find($this->purchasable_id)->values;
            $this->selectedOptionValues = [];
            foreach ($optionValues as $value) {
                $this->selectedOptionValues[$value->product_option_id] = $value->id;
            }
        } else {
            $this->selectedOptionValues = $this->productOptions->mapWithKeys(function ($data) {
                return [$data['option']->id => $data['values']->first()->id];
            })->toArray();
        }

        if (! $this->variant) {
            abort(404);
        }
    }

    public function setSelectedOptionValue(int $optionId, int $valueId): void
    {
        $this->selectedOptionValues[$optionId] = $valueId;
        $this->getVariantProperty();
    }

    /**
     * Computed property to get variant.
     *
     * @return \Lunar\Models\ProductVariant
     */
    public function getVariantProperty()
    {
        $variant = $this->product->variants->first(function ($variant) {
            return ! $variant->values->pluck('id')
                ->diff(
                    collect($this->selectedOptionValues)->values()
                )->count();
        });

        $this->purchasable_id = $variant->id;

        return $variant;
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
        return WebstoreHelperService::priceRangeString(priceCollection: $this->product->prices);
    }

    public function getProductOptionsArray(): array
    {
        return WebstoreHelperService::productOptionsArray(product: $this->product);
    }
}

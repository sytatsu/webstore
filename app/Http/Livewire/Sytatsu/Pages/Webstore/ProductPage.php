<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Services\StorefrontService;
use App\Services\WebstoreHelperService;
use Livewire\Attributes\Url;
use Lunar\Models\Product;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use Lunar\Models\ProductVariant;

class ProductPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.product';

    #[Url(as: 'purchasable_id', except: '')]
    public string $purchasableId = '';
    public Product $product;

    public array $selectedOptionValues = [];

    private StorefrontService $storefrontService;

//    @TODO; Mount does a lot a should be optimized
    public function boot(StorefrontService $storefrontService): void
    {
        $this->storefrontService = $storefrontService;
    }

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->setTitle($product->translateAttribute('name'));

        $this->setViewAttributes([
            'product' => $this->product,
        ]);

        if ($this->purchasableId) {
            $variant = $this->storefrontService->findVariant($this->purchasableId);
            $this->selectedOptionValues = $variant ? $this->storefrontService->getSelectedOptionsForVariant($variant) : [];
        } else {
            $this->selectedOptionValues = $this->storefrontService->getDefaultSelectedOptions($this->product);
        }

        if (! $this->variant) {
            abort(404);
        }
    }

    public function setSelectedOptionValue(int $optionId, int $valueId): void
    {
        $this->selectedOptionValues[$optionId] = $valueId;
        $this->getVariantProperty(); // @TODO; This is only used to update the purchable_id in the query string
    }

    public function getVariantProperty(): ?ProductVariant
    {
        $variant = $this->storefrontService->findVariantByOptions($this->product, $this->selectedOptionValues);

        if ($variant) {
            $this->purchasableId = (string) $variant->id;
        }

        return $variant;
    }

    /**
     * Computed propert to get available product options with values.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProductOptionsProperty(): \Illuminate\Support\Collection
    {
        return $this->storefrontService->getProductOptionsWithValues($this->product);
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

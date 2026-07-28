<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use Lunar\Models\Product;
use Lunar\Models\Url;

class ClickerzBarBuilderPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.clickerz-bar-builder';

    public Product $product;

    public function mount(): void
    {
        $this->product = Url::query()
            ->where('slug', 'clickerz-bar')
            ->whereIn('element_type', [
                (new Product)->getMorphClass(),
                Product::class,
                'product',
            ])
            ->orderBy('default', 'desc')
            ->orderBy('id', 'desc')
            ->firstOrFail()
            ->element;

        $this->setTitle($this->product->translateAttribute('name'));

        $this->setViewAttributes([
            'product' => $this->product,
        ]);
    }
}

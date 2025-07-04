<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

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
    }
}

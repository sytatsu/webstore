<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use Lunar\Models\Product as LunarProduct;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;

class Product extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.product';
    protected ?string $title = 'Print & Shop';

    public LunarProduct $product;

    public function mount(LunarProduct $product): void
    {
        $this->product = $product;
    }
}

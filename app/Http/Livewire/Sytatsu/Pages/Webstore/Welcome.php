<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Product;

class Welcome extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.listing';
    protected ?string $title = 'Print & Shop';

    public ?string $label = null;

    /** @var Collection<ProductPage> $products */
    public Collection $products;

    /** @return Collection<ProductPage> */
    public function getProducts(): Collection
    {
        if (isset($this->products)) {
            return $this->products;
        }

        $this->products = Product::all();

        return $this->products;
    }
}

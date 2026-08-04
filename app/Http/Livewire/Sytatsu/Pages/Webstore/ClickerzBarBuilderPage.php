<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Filament\Pages\BarBuilderSettingsPage;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use Lunar\Models\Product;
use Lunar\Models\Url;

class ClickerzBarBuilderPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.clickerz-bar-builder';

    public Product $product;

    public function mount(): void
    {
        abort_unless(BarBuilderSettingsPage::isEnabled(), 404);

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
        $this->setDescription('Build your own custom Clickerz Bar. Pick your colours, add your icons, and spell out your own word.');
        $this->setImage($this->product->getThumbnailImage());

        $this->setViewAttributes([
            'product' => $this->product,
        ]);
    }
}

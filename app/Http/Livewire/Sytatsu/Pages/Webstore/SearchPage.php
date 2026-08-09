<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\StorefrontService;
use Illuminate\Support\Collection;

class SearchPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.search';
    public string $maxWidth = 'max-w-[85rem]';

    public string $query = '';

    protected $queryString = [
        'query' => ['as' => 'q', 'except' => ''],
    ];

    public function mount(): void
    {
        $this->setTitle(trim($this->query) !== ''
            ? sprintf('%s: "%s"', __('Search'), $this->query)
            : __('Search'));

        $this->setDescription(__('Search results for products at Sytatsu.'));
    }

    public function getProductsProperty(): Collection
    {
        $term = trim($this->query);

        if ($term === '') {
            return collect();
        }

        return app(StorefrontService::class)->searchProducts($term);
    }

    public function getPagesProperty(): Collection
    {
        $term = trim($this->query);

        if ($term === '') {
            return collect();
        }

        return app(StorefrontService::class)->searchPages($term);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
    {
        $this->setViewAttributes([
            'products' => $this->getProductsProperty(),
            'pages' => $this->getPagesProperty(),
            'query' => trim($this->query),
            'maxWidth' => $this->maxWidth,
        ]);

        return parent::render();
    }
}

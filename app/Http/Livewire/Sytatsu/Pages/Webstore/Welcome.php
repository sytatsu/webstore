<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\DTOs\ProductCollectionDTO;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\StorefrontService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class Welcome extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.welcome';
    protected ?string $title = 'Print & Shop';

    public ?string $label = null;

    protected StorefrontService $storefrontService;

    /** @var Collection $products */
    protected Collection $products;

    /** @var SupportCollection<ProductCollectionDTO> $collections */
    protected SupportCollection $collections;

    protected array $collectionIds = [1];

    public string $gridColumns = 'grid-cols-2 lg:grid-cols-4';
    public string $maxWidth = 'max-w-[85rem]';

    public function mount(StorefrontService $storefrontService): void {
        $this->storefrontService = $storefrontService;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
    {
        $this->setViewAttributes([
            'collections' => $this->getCollectionsAttribute(),
            'gridColumns' => 'grid-cols-2 lg:grid-cols-4',
            'maxWidth' => $this->maxWidth,
            'showFilters' => false,
        ]);

        return parent::render();
    }

    public function getCollectionsAttribute(): SupportCollection
    {
        if (!isset($this->collections)) {
            $this->collections = $this->storefrontService->getCollectionsAndDescendantsWithLimitedProducts($this->collectionIds);
        }

        return $this->collections;
    }
}

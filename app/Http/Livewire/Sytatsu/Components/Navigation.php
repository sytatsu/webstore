<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Services\StorefrontService;
use Illuminate\Support\Collection;
use Livewire\Component;

class Navigation extends Component
{
    public Collection $collections;

    public function mount(StorefrontService $storefrontService): void
    {
        $this->collections = $storefrontService->getCollectionTree();
    }

    public function render()
    {
        return view('sytatsu.components.navigation');
    }
}

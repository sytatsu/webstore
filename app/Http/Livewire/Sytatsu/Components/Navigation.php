<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Models\WebstoreSetting;
use App\Services\StorefrontService;
use Illuminate\Support\Collection;
use Livewire\Component;

class Navigation extends Component
{
    public function render(StorefrontService $storefrontService)
    {
        $groupHandles = WebstoreSetting::getByKey('navigation_collection_groups', ['printed']);

        $collections = $storefrontService->getCollectionTreeByGroupHandles($groupHandles)
            ->groupBy(fn ($collection) => $collection->group->id)
            ->values();

        return view('sytatsu.components.navigation', [
            'collections' => $collections,
        ]);
    }
}

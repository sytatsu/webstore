<?php

namespace App\Filament\Extensions;

use App\Filament\Actions\Orders\UpdateOrderStatusAction;
use Lunar\Admin\Livewire\Components\ActivityLogFeed;
use Lunar\Admin\Support\Actions\Orders\UpdateStatusAction;
use Lunar\Admin\Support\Extending\BaseExtension;

class OrderHeaderActionsExtension extends BaseExtension
{
    public function headerActions(array $actions): array
    {
        $page = $this->caller;

        foreach ($actions as $key => $action) {
            if ($action instanceof UpdateStatusAction) {
                $actions[$key] = UpdateOrderStatusAction::make('update_status')
                    ->after(function () use ($page) {
                        $page->dispatch(ActivityLogFeed::UPDATED)->to(ActivityLogFeed::class);
                    });
            }
        }

        return $actions;
    }
}

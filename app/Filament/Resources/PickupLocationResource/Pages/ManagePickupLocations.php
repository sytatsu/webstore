<?php

namespace App\Filament\Resources\PickupLocationResource\Pages;

use App\Filament\Resources\PickupLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePickupLocations extends ManageRecords
{
    protected static string $resource = PickupLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

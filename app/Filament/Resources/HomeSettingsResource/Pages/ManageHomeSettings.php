<?php

namespace App\Filament\Resources\HomeSettingsResource\Pages;

use App\Filament\Resources\HomeSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeSettings extends ManageRecords
{
    protected static string $resource = HomeSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

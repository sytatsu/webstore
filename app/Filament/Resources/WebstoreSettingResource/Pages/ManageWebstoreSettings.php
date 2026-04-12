<?php

namespace App\Filament\Resources\WebstoreSettingResource\Pages;

use App\Filament\Resources\WebstoreSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWebstoreSettings extends ManageRecords
{
    protected static string $resource = WebstoreSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

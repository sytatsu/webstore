<?php

namespace App\Filament\Resources\BundleConfigResource\Pages;

use App\Filament\Resources\BundleConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBundleConfigs extends ListRecords
{
    protected static string $resource = BundleConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

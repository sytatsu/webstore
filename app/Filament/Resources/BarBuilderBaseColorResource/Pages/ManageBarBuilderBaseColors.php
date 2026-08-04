<?php

namespace App\Filament\Resources\BarBuilderBaseColorResource\Pages;

use App\Filament\Resources\BarBuilderBaseColorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBarBuilderBaseColors extends ManageRecords
{
    protected static string $resource = BarBuilderBaseColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

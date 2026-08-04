<?php

namespace App\Filament\Resources\BarBuilderIconResource\Pages;

use App\Filament\Resources\BarBuilderIconResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBarBuilderIcons extends ManageRecords
{
    protected static string $resource = BarBuilderIconResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

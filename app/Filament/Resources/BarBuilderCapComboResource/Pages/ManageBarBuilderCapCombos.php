<?php

namespace App\Filament\Resources\BarBuilderCapComboResource\Pages;

use App\Filament\Resources\BarBuilderCapComboResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBarBuilderCapCombos extends ManageRecords
{
    protected static string $resource = BarBuilderCapComboResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

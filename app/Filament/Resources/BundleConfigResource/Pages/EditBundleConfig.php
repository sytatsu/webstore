<?php

namespace App\Filament\Resources\BundleConfigResource\Pages;

use App\Filament\Resources\BundleConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBundleConfig extends EditRecord
{
    protected static string $resource = BundleConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

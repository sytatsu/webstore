<?php

namespace App\Filament\Resources\NotificationBannerResource\Pages;

use App\Filament\Resources\NotificationBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNotificationBanners extends ManageRecords
{
    protected static string $resource = NotificationBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

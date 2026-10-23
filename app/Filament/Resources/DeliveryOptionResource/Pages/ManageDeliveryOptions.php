<?php

namespace App\Filament\Resources\DeliveryOptionResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Actions\CreateAction;
use App\Filament\Resources\DeliveryOptionResource;
use App\Models\WebstoreSetting;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageDeliveryOptions extends ManageRecords
{
    protected static string $resource = DeliveryOptionResource::class;

    public const FREE_SHIPPING_THRESHOLD_KEY = 'free_shipping_threshold';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('freeShippingThreshold')
                ->label('Free shipping threshold')
                ->icon('heroicon-o-currency-euro')
                ->color('gray')
                ->schema([
                    TextInput::make('threshold')
                        ->label('Free shipping threshold (in cents)')
                        ->helperText('Once a cart\'s total passes this amount, delivery options marked "Free shipping option" are shown instead of the paid options.')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                ])
                ->fillForm(fn () => [
                    'threshold' => WebstoreSetting::getByKey(self::FREE_SHIPPING_THRESHOLD_KEY, 8000),
                ])
                ->action(function (array $data) {
                    WebstoreSetting::setByKey(self::FREE_SHIPPING_THRESHOLD_KEY, (int) $data['threshold']);

                    Notification::make()
                        ->title('Free shipping threshold updated')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}

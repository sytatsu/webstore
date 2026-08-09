<?php

namespace App\Filament\Actions\Orders;

use App\Enums\ShippingCarrierEnum;
use Filament\Forms;
use Lunar\Admin\Support\Actions\Orders\UpdateStatusAction;
use Lunar\Models\Order;

class UpdateOrderStatusAction extends UpdateStatusAction
{
    protected function getFormSteps(): array
    {
        return [
            static::getStatusSelectInput(),
            static::getCarrierInput(),
            static::getTrackingNumberInput(),
            Forms\Components\Group::make([
                static::getMailersCheckboxInput(),
                Forms\Components\Group::make([
                    static::getAdditionalContentInput(),
                    static::getEmailAddressesInput(),
                    static::getAdditionalEmailInput(),
                ])->hidden(function (Forms\Get $get) {
                    return ! count($get('mailers')) ||
                        ! count(static::getMailers($get('status')));
                }),
            ])->hidden(function (Forms\Get $get) {
                return ! count(static::getMailers($get('status')));
            }),
        ];
    }

    protected static function getCarrierInput(): Forms\Components\Select
    {
        return Forms\Components\Select::make('carrier')
            ->label('Carrier')
            ->options(collect(ShippingCarrierEnum::cases())->mapWithKeys(
                fn (ShippingCarrierEnum $carrier) => [$carrier->value => $carrier->label()]
            ))
            ->default(fn (?Order $record) => $record?->carrier)
            ->required(fn (Forms\Get $get) => $get('status') === 'dispatched')
            ->visible(fn (Forms\Get $get) => $get('status') === 'dispatched');
    }

    protected static function getTrackingNumberInput(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('tracking_number')
            ->label('Track & Trace number')
            ->default(fn (?Order $record) => $record?->tracking_number)
            ->required(fn (Forms\Get $get) => $get('status') === 'dispatched')
            ->visible(fn (Forms\Get $get) => $get('status') === 'dispatched');
    }

    protected function updateStatus(Order $record, array $data)
    {
        if ($data['status'] === 'dispatched') {
            $record->update([
                'carrier' => $data['carrier'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
            ]);
        }

        parent::updateStatus($record, $data);
    }
}

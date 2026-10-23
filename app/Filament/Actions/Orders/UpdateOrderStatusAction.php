<?php

namespace App\Filament\Actions\Orders;

use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
            Group::make([
                static::getMailersCheckboxInput(),
                Group::make([
                    static::getAdditionalContentInput(),
                    static::getEmailAddressesInput(),
                    static::getAdditionalEmailInput(),
                ])->hidden(function (Get $get) {
                    return ! count($get('mailers')) ||
                        ! count(static::getMailers($get('status')));
                }),
            ])->hidden(function (Get $get) {
                return ! count(static::getMailers($get('status')));
            }),
        ];
    }

    protected static function getCarrierInput(): Select
    {
        return Select::make('carrier')
            ->label('Carrier')
            ->options(collect(ShippingCarrierEnum::cases())->mapWithKeys(
                fn (ShippingCarrierEnum $carrier) => [$carrier->value => $carrier->label()]
            ))
            ->default(fn (?Order $record) => $record?->carrier)
            ->required(fn (Get $get) => $get('status') === 'dispatched')
            ->visible(fn (Get $get) => $get('status') === 'dispatched');
    }

    protected static function getTrackingNumberInput(): TextInput
    {
        return TextInput::make('tracking_number')
            ->label('Track & Trace number')
            ->default(fn (?Order $record) => $record?->tracking_number)
            ->required(fn (Get $get) => $get('status') === 'dispatched')
            ->visible(fn (Get $get) => $get('status') === 'dispatched');
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

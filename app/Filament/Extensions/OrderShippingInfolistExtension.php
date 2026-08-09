<?php

namespace App\Filament\Extensions;

use App\Enums\ShippingCarrierEnum;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Lunar\Admin\Support\Extending\BaseExtension;
use Lunar\Models\Order;

class OrderShippingInfolistExtension extends BaseExtension
{
    public function extendShippingInfolist(Section $section): Section
    {
        return $section->schema([
            ...$section->getChildComponents(),
            TextEntry::make('carrier')
                ->label('Carrier')
                ->icon('heroicon-s-truck')
                ->formatStateUsing(fn (?string $state) => ShippingCarrierEnum::tryFrom($state)?->label() ?? $state)
                ->hidden(fn (?string $state) => blank($state)),
            TextEntry::make('tracking_number')
                ->label('Track & Trace number')
                ->icon('heroicon-s-link')
                ->url(fn (Order $record) => $record->carrier && $record->tracking_number
                    ? ShippingCarrierEnum::tryFrom($record->carrier)?->trackingUrl($record->tracking_number)
                    : null)
                ->openUrlInNewTab()
                ->hidden(fn (?string $state) => blank($state)),
        ]);
    }
}

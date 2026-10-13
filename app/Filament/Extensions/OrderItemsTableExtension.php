<?php

namespace App\Filament\Extensions;

use App\Models\BarBuilderIcon;
use App\Support\PrintOnDemand;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lunar\Admin\Support\Extending\BaseExtension;
use Lunar\Models\OrderLine;

class OrderItemsTableExtension extends BaseExtension
{
    public function extendTable(Table $table): Table
    {
        return $table->actions([
            Action::make('barBuilderPreview')
                ->label(__('Preview'))
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->visible(fn (OrderLine $record) => filled($record->meta['bar_builder'] ?? null))
                ->modalHeading(__('Clickerz Bar configuration'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('Close'))
                ->modalContent(fn (OrderLine $record) => view('filament.orders.bar-builder-preview', [
                    'barBuilder' => $record->meta['bar_builder'],
                    'icons' => BarBuilderIcon::query()
                        ->whereIn('id', $this->iconIds($record))
                        ->get()
                        ->keyBy('id'),
                ])),
            Action::make('bundleContentsPreview')
                ->label(__('Bundle contents'))
                ->icon('heroicon-o-gift-top')
                ->color('gray')
                ->visible(fn (OrderLine $record) => filled($record->meta['bundle'] ?? null))
                ->modalHeading(__('Bundle contents'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('Close'))
                ->modalContent(fn (OrderLine $record) => view('filament.orders.bundle-preview', [
                    'bundle' => $record->meta['bundle'],
                ])),
        ]);
    }

    /**
     * Flags the lines (or the part of a line) that weren't in stock when the
     * order was placed and still need printing — slotted in right under the
     * line's description/price row, above Lunar's collapsible details panel.
     */
    public function extendOrderLinesTableColumns(array $columns): array
    {
        array_splice($columns, 1, 0, [
            TextColumn::make('meta.'.PrintOnDemand::META_KEY)
                ->badge()
                ->color('warning')
                ->icon('heroicon-o-printer')
                ->formatStateUsing(fn ($state, OrderLine $record) => (int) $state >= $record->quantity
                    ? __('Print on demand')
                    : __(':count of :quantity printed on demand', ['count' => (int) $state, 'quantity' => $record->quantity])),
        ]);

        return $columns;
    }

    private function iconIds(OrderLine $record): array
    {
        return collect($record->meta['bar_builder']['caps'] ?? [])
            ->map(fn (array $cap) => $cap['icon']['id'] ?? null)
            ->filter()
            ->values()
            ->all();
    }
}

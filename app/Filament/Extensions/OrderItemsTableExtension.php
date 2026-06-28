<?php

namespace App\Filament\Extensions;

use App\Models\BundleConfig;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Lunar\Admin\Support\Extending\BaseExtension;

class OrderItemsTableExtension extends BaseExtension
{
    public function extendOrderLinesTableColumns(array $columns): array
    {
        $columns[] = Tables\Columns\Layout\Panel::make([
            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('bundle_group')
                    ->label('Bundle')
                    ->getStateUsing(function ($record) {
                        $bundleConfigId = data_get($record->meta, 'bundle_config_id');
                        $name = $bundleConfigId
                            ? (BundleConfig::find($bundleConfigId)?->getTranslatedName() ?: __('Bundle'))
                            : __('Bundle');
                        $discountPct = data_get($record->meta, 'bundle_discount_pct', 0);

                        return $discountPct > 0
                            ? $name . ' · -' . number_format($discountPct, 0) . '%'
                            : $name;
                    })
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('bundle_id_short')
                    ->label(__('Bundle ID'))
                    ->getStateUsing(fn($record) => '#' . substr(data_get($record->meta, 'bundle_id', ''), 0, 8))
                    ->color(Color::Gray),
            ]),
        ])
            ->collapsed(false)
            ->hidden(fn($record) => empty(data_get($record->meta, 'bundle_id')));

        return $columns;
    }
}

<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\BarBuilderBaseColorResource\Pages\ManageBarBuilderBaseColors;
use App\Filament\Clusters\BarBuilder;
use App\Filament\Resources\BarBuilderBaseColorResource\Pages;
use App\Filament\Resources\Concerns\HasTranslatableName;
use App\Models\BarBuilderBaseColor;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class BarBuilderBaseColorResource extends Resource
{
    use HasTranslatableName;

    protected static ?string $cluster = BarBuilder::class;

    protected static ?string $model = BarBuilderBaseColor::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Base Colours';

    protected static ?string $modelLabel = 'base colour';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Base colour')
                    ->description('The colour options for the bar itself (the base the caps sit in).')
                    ->schema([
                        static::nameFormFieldset('e.g. Jet', 'e.g. Gitzwart'),
                        ColorPicker::make('hex')
                            ->required(),
                        Toggle::make('enabled')
                            ->label('Available to customers')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ColorColumn::make('hex'),
                static::nameTableColumn(),
                TextColumn::make('hex')
                    ->label('Hex')
                    ->fontFamily('mono'),
                ToggleColumn::make('enabled'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBarBuilderBaseColors::route('/'),
        ];
    }
}

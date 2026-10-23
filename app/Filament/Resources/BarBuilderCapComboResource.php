<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\BarBuilderCapComboResource\Pages\ManageBarBuilderCapCombos;
use App\Filament\Clusters\BarBuilder;
use App\Filament\Resources\BarBuilderCapComboResource\Pages;
use App\Filament\Resources\Concerns\HasTranslatableName;
use App\Models\BarBuilderCapCombo;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ToggleColumn;

class BarBuilderCapComboResource extends Resource
{
    use HasTranslatableName;

    protected static ?string $cluster = BarBuilder::class;

    protected static ?string $model = BarBuilderCapCombo::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationLabel = 'Cap Colour Combinations';

    protected static ?string $modelLabel = 'cap colour combination';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cap colour combination')
                    ->description('A manufacturable cap + letter colour pairing customers can pick for each cap.')
                    ->schema([
                        static::nameFormFieldset('e.g. Ember', 'e.g. Gloed'),
                        ColorPicker::make('cap_hex')
                            ->label('Cap colour')
                            ->required(),
                        ColorPicker::make('text_hex')
                            ->label('Letter colour')
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
                ColorColumn::make('cap_hex')
                    ->label('Cap'),
                ColorColumn::make('text_hex')
                    ->label('Letter'),
                static::nameTableColumn(),
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
            'index' => ManageBarBuilderCapCombos::route('/'),
        ];
    }
}

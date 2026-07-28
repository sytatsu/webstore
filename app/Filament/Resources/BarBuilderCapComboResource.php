<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\BarBuilder;
use App\Filament\Resources\BarBuilderCapComboResource\Pages;
use App\Models\BarBuilderCapCombo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class BarBuilderCapComboResource extends Resource
{
    protected static ?string $cluster = BarBuilder::class;

    protected static ?string $model = BarBuilderCapCombo::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationLabel = 'Cap Colour Combinations';

    protected static ?string $modelLabel = 'cap colour combination';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cap colour combination')
                    ->description('A manufacturable cap + letter colour pairing customers can pick for each cap.')
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('e.g. Ember')
                            ->required(),
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('enabled'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBarBuilderCapCombos::route('/'),
        ];
    }
}

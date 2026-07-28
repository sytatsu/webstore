<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\BarBuilder;
use App\Filament\Resources\BarBuilderBaseColorResource\Pages;
use App\Models\BarBuilderBaseColor;
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

class BarBuilderBaseColorResource extends Resource
{
    protected static ?string $cluster = BarBuilder::class;

    protected static ?string $model = BarBuilderBaseColor::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Base Colours';

    protected static ?string $modelLabel = 'base colour';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Base colour')
                    ->description('The colour options for the bar itself (the base the caps sit in).')
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('e.g. Jet')
                            ->required(),
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hex')
                    ->label('Hex')
                    ->fontFamily('mono'),
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
            'index' => Pages\ManageBarBuilderBaseColors::route('/'),
        ];
    }
}

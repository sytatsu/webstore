<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\BarBuilder;
use App\Filament\Resources\BarBuilderIconResource\Pages;
use App\Models\BarBuilderIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class BarBuilderIconResource extends Resource
{
    protected static ?string $cluster = BarBuilder::class;

    protected static ?string $model = BarBuilderIcon::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Icons';

    protected static ?string $modelLabel = 'icon';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Icon')
                    ->description('An SVG glyph customers can engrave on a cap instead of a letter.')
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('e.g. Dog paw')
                            ->required(),
                        Textarea::make('svg_paths')
                            ->label('SVG path data')
                            ->helperText('One SVG "d" path per line, authored on a 0-100 canvas. Multiple lines are layered into one icon.')
                            ->rows(4)
                            ->required()
                            ->afterStateHydrated(function (Textarea $component, $state) {
                                $component->state(is_array($state) ? implode("\n", $state) : $state);
                            })
                            ->dehydrateStateUsing(fn ($state) => array_values(array_filter(array_map('trim', explode("\n", (string) $state))))),
                        TextInput::make('cx')
                            ->label('Centre X')
                            ->numeric()
                            ->default(50)
                            ->required(),
                        TextInput::make('cy')
                            ->label('Centre Y')
                            ->numeric()
                            ->default(50)
                            ->required(),
                        TextInput::make('scale')
                            ->numeric()
                            ->step(0.01)
                            ->default(1)
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
            'index' => Pages\ManageBarBuilderIcons::route('/'),
        ];
    }
}

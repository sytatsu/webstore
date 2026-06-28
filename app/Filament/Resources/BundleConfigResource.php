<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\WebstoreSettings;
use App\Filament\Resources\BundleConfigResource\Pages;
use App\Models\BundleConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Lunar\Admin\Support\Forms\Components\TranslatedText;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Lunar\Models\Collection;

class BundleConfigResource extends Resource
{
    protected static ?string $cluster = WebstoreSettings::class;

    protected static ?string $model = BundleConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Bundle Configs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bundle Configuration')
                    ->schema([
                        TranslatedText::make('bundle_name')
                            ->label('Bundle Name')
                            ->placeholder('e.g. Summer Bundle')
                            ->columnSpanFull(),
                        Select::make('collection_id')
                            ->label('Collection')
                            ->options(fn() => Collection::query()
                                ->get()
                                ->mapWithKeys(fn(Collection $c) => [$c->id => $c->translateAttribute('name')])
                            )
                            ->required()
                            ->searchable()
                            ->unique(ignoreRecord: true),
                        Toggle::make('enabled')
                            ->label('Enabled')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Discount Tiers')
                    ->schema([
                        Repeater::make('discount_tiers')
                            ->label('Tiers')
                            ->schema([
                                TextInput::make('min_quantity')
                                    ->label('Minimum Quantity')
                                    ->integer()
                                    ->required()
                                    ->minValue(1),
                                TextInput::make('discount_pct')
                                    ->label('Discount %')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('collection.id')
                    ->label('Collection')
                    ->formatStateUsing(fn($record) => $record->collection?->translateAttribute('name') ?? '—')
                    ->searchable(false)
                    ->sortable(),
                IconColumn::make('enabled')
                    ->boolean(),
                TextColumn::make('discount_tiers')
                    ->label('Tiers')
                    ->formatStateUsing(fn($state) => is_array($state) ? count($state) . ' tier(s)' : '0 tiers'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListBundleConfigs::route('/'),
            'create' => Pages\CreateBundleConfig::route('/create'),
            'edit' => Pages\EditBundleConfig::route('/{record}/edit'),
        ];
    }
}

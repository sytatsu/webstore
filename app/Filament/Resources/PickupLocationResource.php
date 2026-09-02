<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ShippingOptions;
use App\Filament\Resources\PickupLocationResource\Pages;
use App\Models\PickupLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PickupLocationResource extends Resource
{
    protected static ?string $model = PickupLocation::class;

    protected static ?string $cluster = ShippingOptions::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Pickup Locations';

    protected static ?string $modelLabel = 'pickup location';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pickup Location')
                    ->description('A physical location customers can select at checkout instead of having their order shipped.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, Forms\Set $set, ?PickupLocation $record) {
                                if (! $record) {
                                    $set('identifier', 'PICKUP_'.Str::upper(Str::slug($state, '_')));
                                }
                            }),
                        Forms\Components\TextInput::make('identifier')
                            ->label('Identifier')
                            ->helperText('Used internally to identify this location. Cannot be changed after creation.')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?PickupLocation $record) => $record !== null)
                            ->dehydrated(),
                        Forms\Components\TextInput::make('address_line_1')
                            ->label('Address line 1')
                            ->required(),
                        Forms\Components\TextInput::make('address_line_2')
                            ->label('Address line 2'),
                        Forms\Components\TextInput::make('postcode')
                            ->required(),
                        Forms\Components\TextInput::make('city')
                            ->required(),
                        Forms\Components\TextInput::make('country')
                            ->required()
                            ->default('Netherlands'),
                        Forms\Components\Fieldset::make('Availability note')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                Forms\Components\Textarea::make('availability_note.en')
                                    ->label('Availability note (English)')
                                    ->helperText("Shown to customers at checkout and in the pickup email, e.g. \"Available Fridays & Saturdays, we'll confirm the exact day by email.\"")
                                    ->rows(2)
                                    ->required(),
                                Forms\Components\Textarea::make('availability_note.nl')
                                    ->label('Availability note (Dutch)')
                                    ->helperText('Falls back to English when left empty.')
                                    ->rows(2)
                                    ->dehydrated(fn (?string $state): bool => filled($state)),
                            ]),
                        Forms\Components\TextInput::make('price')
                            ->label('Price (in cents)')
                            ->helperText('0 = free pickup.')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\Toggle::make('enabled')
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
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\ToggleColumn::make('enabled'),
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
            'index' => Pages\ManagePickupLocations::route('/'),
        ];
    }
}

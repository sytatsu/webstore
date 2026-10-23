<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PickupLocationResource\Pages\ManagePickupLocations;
use App\Filament\Clusters\ShippingOptions;
use App\Filament\Resources\PickupLocationResource\Pages;
use App\Models\PickupLocation;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PickupLocationResource extends Resource
{
    protected static ?string $model = PickupLocation::class;

    protected static ?string $cluster = ShippingOptions::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Pickup Locations';

    protected static ?string $modelLabel = 'pickup location';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pickup Location')
                    ->description('A physical location customers can select at checkout instead of having their order shipped.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, Set $set, ?PickupLocation $record) {
                                if (! $record) {
                                    $set('identifier', 'PICKUP_'.Str::upper(Str::slug($state, '_')));
                                }
                            }),
                        TextInput::make('identifier')
                            ->label('Identifier')
                            ->helperText('Used internally to identify this location. Cannot be changed after creation.')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?PickupLocation $record) => $record !== null)
                            ->dehydrated(),
                        TextInput::make('address_line_1')
                            ->label('Address line 1')
                            ->required(),
                        TextInput::make('address_line_2')
                            ->label('Address line 2'),
                        TextInput::make('postcode')
                            ->required(),
                        TextInput::make('city')
                            ->required(),
                        TextInput::make('country')
                            ->required()
                            ->default('Netherlands'),
                        Fieldset::make('Availability note')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                Textarea::make('availability_note.en')
                                    ->label('Availability note (English)')
                                    ->helperText("Shown to customers at checkout and in the pickup email, e.g. \"Available Fridays & Saturdays, we'll confirm the exact day by email.\"")
                                    ->rows(2)
                                    ->required(),
                                Textarea::make('availability_note.nl')
                                    ->label('Availability note (Dutch)')
                                    ->helperText('Falls back to English when left empty.')
                                    ->rows(2)
                                    ->dehydrated(fn (?string $state): bool => filled($state)),
                            ]),
                        TextInput::make('price')
                            ->label('Price (in cents)')
                            ->helperText('0 = free pickup.')
                            ->numeric()
                            ->default(0)
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
                TextColumn::make('name'),
                TextColumn::make('city'),
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
            'index' => ManagePickupLocations::route('/'),
        ];
    }
}

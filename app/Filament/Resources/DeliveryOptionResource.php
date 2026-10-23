<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\DeliveryOptionResource\Pages\ManageDeliveryOptions;
use App\Enums\ShippingCarrierEnum;
use App\Filament\Clusters\ShippingOptions;
use App\Filament\Resources\DeliveryOptionResource\Pages;
use App\Models\DeliveryOption;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeliveryOptionResource extends Resource
{
    protected static ?string $model = DeliveryOption::class;

    protected static ?string $cluster = ShippingOptions::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Delivery Options';

    protected static ?string $modelLabel = 'delivery option';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Delivery Option')
                    ->description('A carrier delivery tier customers can choose at checkout.')
                    ->schema([
                        Select::make('carrier')
                            ->label('Carrier')
                            ->options(collect(ShippingCarrierEnum::cases())->mapWithKeys(
                                fn (ShippingCarrierEnum $carrier) => [$carrier->value => $carrier->label()]
                            ))
                            ->required(),
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('identifier')
                            ->label('Identifier')
                            ->helperText('Used internally to identify this option. Cannot be changed after creation.')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?DeliveryOption $record) => $record !== null)
                            ->dehydrated(),
                        TextInput::make('price')
                            ->label('Price (in cents)')
                            ->helperText('0 = free.')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('free_shipping')
                            ->label('Free shipping option')
                            ->helperText("When enabled, this option is shown to customers instead of this carrier's paid options once their cart total passes the free shipping threshold (configurable from the overview page).")
                            ->default(false),
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
            ->defaultSort(fn (Builder $query) => $query->orderBy('carrier')->orderBy('sort_order'))
            ->columns([
                TextColumn::make('carrier')
                    ->badge()
                    ->formatStateUsing(fn (ShippingCarrierEnum $state) => $state->label()),
                TextColumn::make('name'),
                TextColumn::make('price')
                    ->money('EUR', divideBy: 100),
                IconColumn::make('free_shipping')
                    ->label('Free shipping')
                    ->boolean(),
                ToggleColumn::make('enabled'),
            ])
            ->filters([
                SelectFilter::make('carrier')
                    ->options(collect(ShippingCarrierEnum::cases())->mapWithKeys(
                        fn (ShippingCarrierEnum $carrier) => [$carrier->value => $carrier->label()]
                    )),
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
            'index' => ManageDeliveryOptions::route('/'),
        ];
    }
}

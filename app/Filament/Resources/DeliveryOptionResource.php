<?php

namespace App\Filament\Resources;

use App\Enums\ShippingCarrierEnum;
use App\Filament\Clusters\ShippingOptions;
use App\Filament\Resources\DeliveryOptionResource\Pages;
use App\Models\DeliveryOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeliveryOptionResource extends Resource
{
    protected static ?string $model = DeliveryOption::class;

    protected static ?string $cluster = ShippingOptions::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Delivery Options';

    protected static ?string $modelLabel = 'delivery option';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Delivery Option')
                    ->description('A carrier delivery tier customers can choose at checkout.')
                    ->schema([
                        Forms\Components\Select::make('carrier')
                            ->label('Carrier')
                            ->options(collect(ShippingCarrierEnum::cases())->mapWithKeys(
                                fn (ShippingCarrierEnum $carrier) => [$carrier->value => $carrier->label()]
                            ))
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('identifier')
                            ->label('Identifier')
                            ->helperText('Used internally to identify this option. Cannot be changed after creation.')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?DeliveryOption $record) => $record !== null)
                            ->dehydrated(),
                        Forms\Components\TextInput::make('price')
                            ->label('Price (in cents)')
                            ->helperText('0 = free.')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\Toggle::make('free_shipping')
                            ->label('Free shipping option')
                            ->helperText("When enabled, this option is shown to customers instead of this carrier's paid options once their cart total passes the free shipping threshold (configurable from the overview page).")
                            ->default(false),
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
            ->defaultSort(fn (Builder $query) => $query->orderBy('carrier')->orderBy('sort_order'))
            ->columns([
                Tables\Columns\TextColumn::make('carrier')
                    ->badge()
                    ->formatStateUsing(fn (ShippingCarrierEnum $state) => $state->label()),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('price')
                    ->money('EUR', divideBy: 100),
                Tables\Columns\IconColumn::make('free_shipping')
                    ->label('Free shipping')
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('enabled'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('carrier')
                    ->options(collect(ShippingCarrierEnum::cases())->mapWithKeys(
                        fn (ShippingCarrierEnum $carrier) => [$carrier->value => $carrier->label()]
                    )),
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
            'index' => Pages\ManageDeliveryOptions::route('/'),
        ];
    }
}

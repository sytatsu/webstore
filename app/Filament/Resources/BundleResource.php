<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Bundles;
use App\Filament\Resources\BundleResource\Pages;
use App\Filament\Resources\Concerns\HasTranslatableName;
use App\Models\Bundle;
use App\Services\BundleService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Collection;

class BundleResource extends Resource
{
    use HasTranslatableName;

    protected static ?string $cluster = Bundles::class;

    protected static ?string $model = Bundle::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift-top';

    protected static ?string $navigationLabel = 'Bundles';

    protected static ?string $modelLabel = 'bundle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bundle')
                    ->description('Lets customers mix-and-match products from this collection (and its sub-collections) into one bundle at a quantity-based price.')
                    ->schema([
                        Forms\Components\Select::make('collection_id')
                            ->label('Collection')
                            ->options(fn () => Collection::query()->get()
                                ->mapWithKeys(fn (Collection $collection) => [$collection->id => $collection->translateAttribute('name')]))
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Products in this collection AND any of its sub-collections become eligible for the bundle.'),
                        static::nameFormFieldset('e.g. Mini-friends bundle', 'e.g. Mini-friends bundel'),
                        Forms\Components\Toggle::make('enabled')
                            ->label('Enabled')
                            ->default(false),
                        Forms\Components\TextInput::make('max_items')
                            ->label('Maximum items per bundle')
                            ->helperText('Leave empty for no limit.')
                            ->numeric()
                            ->minValue(1),
                    ])->columns(2),
                Forms\Components\Section::make('Pricing tiers')
                    ->description('The price per item once a customer reaches each quantity. A tier for quantity 1 is required — that\'s the starting (undiscounted) price.')
                    ->schema([
                        Forms\Components\Repeater::make('tiers')
                            ->label('Tiers')
                            ->schema([
                                Forms\Components\TextInput::make('min_quantity')
                                    ->label('From quantity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->label('Price per item')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->prefix('€')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->reorderable(false)
                            ->minItems(1)
                            ->addActionLabel('Add tier')
                            ->rules([
                                fn (): \Closure => function (string $attribute, $value, \Closure $fail) {
                                    if (!collect($value)->contains(fn (array $tier) => (int) ($tier['min_quantity'] ?? 0) === 1)) {
                                        $fail('One tier must have "From quantity" set to 1 — that\'s the base price.');
                                    }
                                },
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                static::nameTableColumn(),
                Tables\Columns\TextColumn::make('collection.attribute_data')
                    ->label('Collection')
                    ->getStateUsing(fn (Bundle $record) => $record->collection?->translateAttribute('name') ?? '—'),
                Tables\Columns\ToggleColumn::make('enabled'),
                Tables\Columns\TextColumn::make('max_items')
                    ->label('Max items')
                    ->placeholder('No limit'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Bundle $record): array {
                        $data['tiers'] = $record->tiers()
                            ->map(fn ($price) => [
                                'min_quantity' => $price->min_quantity,
                                'price' => $price->price->value / (10 ** ($price->currency?->decimal_places ?? 2)),
                            ])
                            ->values()
                            ->all();

                        return $data;
                    })
                    ->using(function (array $data, Bundle $record): Model {
                        $tiers = $data['tiers'] ?? [];
                        unset($data['tiers']);

                        $record->update($data);
                        app(BundleService::class)->syncPurchasable($record, $tiers);

                        return $record;
                    }),
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
            'index' => Pages\ManageBundles::route('/'),
        ];
    }
}

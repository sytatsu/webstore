<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\WebstoreSettings;
use App\Filament\Resources\DeliveryOptionResource\Pages\ManageDeliveryOptions;
use App\Filament\Resources\WebstoreSettingResource\Pages;
use App\Models\WebstoreSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Tables\Columns\TextColumn;
use Lunar\Admin\Support\Forms\Components\TranslatedText;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Lunar\Models\Collection;

class WebstoreSettingResource extends Resource
{
    protected static ?string $cluster = WebstoreSettings::class;

    protected static ?string $model = WebstoreSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'General Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Setting')
                    ->schema([
                        TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record !== null),

                        TagsInput::make('value')
                            ->label('Value(s)')
                            ->helperText('Add one or more values for this setting (e.g. collection group handles).')
                            ->visible(fn ($get) => in_array($get('key'), ['navigation_collection_groups']))
                            ->required(),

                        TextInput::make('value')
                            ->label('Value')
                            ->helperText('Once a cart\'s total (in cents) passes this amount, delivery options marked "Free shipping option" are shown instead of the paid options. Also editable via the popup on the Delivery Options overview.')
                            ->numeric()
                            ->visible(fn ($get) => $get('key') === ManageDeliveryOptions::FREE_SHIPPING_THRESHOLD_KEY)
                            ->required(),

                        TranslatedText::make('value')
                            ->label('Value')
                            ->visible(fn ($get) => false) // No longer using translated text for home titles
                            ->required(),

                        Repeater::make('value')
                            ->label('Collections')
                            ->schema([
                                Select::make('collection_id')
                                    ->label('Collection')
                                    ->options(function () {
                                        return Collection::all()->mapWithKeys(function ($collection) {
                                            $name = $collection->translateAttribute('name') ?? "Collection #{$collection->id}";
                                            return [$collection->id => (string) $name];
                                        });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->afterStateHydrated(function (Repeater $component, $state) {
                                if (is_array($state) && !empty($state) && !isset($state[0]['collection_id'])) {
                                    $component->state(collect($state)->map(fn($id) => ['collection_id' => $id])->toArray());
                                }
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return collect($state)->pluck('collection_id')->toArray();
                            })
                            ->visible(fn ($get) => in_array($get('key'), ['collections_page_collections', 'home_featured_collections']))
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Values')
                    ->badge()
                    ->separator(',')
                    ->getStateUsing(function ($record) {
                        $value = $record->value;
                        if (!is_array($value)) {
                            return (string) $value;
                        }

                        // Check if it's a translation array (keys are locale codes)
                        $locales = ['en', 'nl'];
                        $isTranslation = false;
                        foreach ($value as $k => $v) {
                            if (in_array($k, $locales)) {
                                $isTranslation = true;
                                break;
                            }
                        }

                        if ($isTranslation) {
                            return $value[app()->getLocale()] ?? $value[config('app.fallback_locale', 'en')] ?? collect($value)->first();
                        }

                        // Otherwise, it's a list (like navigation_collection_groups or home_featured_collections)
                        // Ensure all elements are strings or numbers
                        return collect($value)->map(function ($item) {
                            if (is_array($item)) {
                                return json_encode($item);
                            }
                            return (string) $item;
                        })->toArray();
                    }),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => WebstoreSetting::isProtected($record->key)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Tables\Actions\DeleteBulkAction $action, \Illuminate\Support\Collection $records) {
                            $protectedRecords = $records->filter(fn ($record) => WebstoreSetting::isProtected($record->key));
                            if ($protectedRecords->count()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Failed to delete some settings')
                                    ->body('One or more of the selected settings are protected and cannot be deleted.')
                                    ->danger()
                                    ->send();
                                $action->halt();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWebstoreSettings::route('/'),
        ];
    }
}

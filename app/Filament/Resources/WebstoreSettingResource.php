<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\WebstoreSettings;
use App\Filament\Pages\BarBuilderDefaultArrangementPage;
use App\Filament\Pages\BarBuilderSettingsPage;
use App\Filament\Pages\CollectionsPageSettingsPage;
use App\Filament\Pages\HomeFeaturedCollectionsSettingsPage;
use App\Filament\Pages\HomepageHeroSettingsPage;
use App\Filament\Pages\NavigationSettingsPage;
use App\Filament\Resources\DeliveryOptionResource\Pages\ManageDeliveryOptions;
use App\Filament\Resources\WebstoreSettingResource\Pages;
use App\Models\WebstoreSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class WebstoreSettingResource extends Resource
{
    protected static ?string $cluster = WebstoreSettings::class;

    protected static ?string $model = WebstoreSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'General Settings';

    /**
     * Keys with a dedicated admin page — proper typed fields, reordering
     * where order matters — rather than the generic JSON editor below.
     * Edited there only, so this resource can't save a shape the
     * dedicated page doesn't expect.
     */
    public const DEDICATED_PAGE_KEYS = [
        NavigationSettingsPage::GROUPS_KEY => NavigationSettingsPage::class,
        NavigationSettingsPage::FDM_PRINTING_KEY => NavigationSettingsPage::class,
        HomeFeaturedCollectionsSettingsPage::SETTING_KEY => HomeFeaturedCollectionsSettingsPage::class,
        CollectionsPageSettingsPage::TITLE_KEY => CollectionsPageSettingsPage::class,
        CollectionsPageSettingsPage::DESCRIPTION_KEY => CollectionsPageSettingsPage::class,
        CollectionsPageSettingsPage::COLLECTIONS_KEY => CollectionsPageSettingsPage::class,
        HomepageHeroSettingsPage::SETTING_KEY => HomepageHeroSettingsPage::class,
        BarBuilderSettingsPage::SETTING_KEY => BarBuilderSettingsPage::class,
        BarBuilderDefaultArrangementPage::SETTING_KEY => BarBuilderDefaultArrangementPage::class,
    ];

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

                        Forms\Components\Placeholder::make('dedicated_page_notice')
                            ->label('')
                            ->content(fn ($get) => "This setting is managed on its own admin page (\"" . class_basename(static::DEDICATED_PAGE_KEYS[$get('key')] ?? '') . "\") to keep its data in the shape that page expects — edit it there instead.")
                            ->visible(fn ($get) => array_key_exists($get('key'), static::DEDICATED_PAGE_KEYS)),

                        TextInput::make('value')
                            ->label('Value')
                            ->helperText('Once a cart\'s total (in cents) passes this amount, delivery options marked "Free shipping option" are shown instead of the paid options. Also editable via the popup on the Delivery Options overview.')
                            ->numeric()
                            ->visible(fn ($get) => $get('key') === ManageDeliveryOptions::FREE_SHIPPING_THRESHOLD_KEY)
                            ->required(),

                        Textarea::make('value')
                            ->label('Value (JSON)')
                            ->helperText('Raw JSON for this setting — a plain value ("true", "some text"), a list (["a", "b"]), or an object ({"a": "b"}).')
                            ->rows(4)
                            ->formatStateUsing(fn ($state) => is_string($state) ? $state : json_encode($state, JSON_PRETTY_PRINT))
                            ->dehydrateStateUsing(fn ($state) => json_decode($state, true) ?? $state)
                            ->rule('json')
                            ->visible(fn ($get) => $get('key')
                                && ! array_key_exists($get('key'), static::DEDICATED_PAGE_KEYS)
                                && $get('key') !== ManageDeliveryOptions::FREE_SHIPPING_THRESHOLD_KEY)
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

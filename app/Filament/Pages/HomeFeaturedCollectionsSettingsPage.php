<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\WebstoreSettings;
use App\Models\WebstoreSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Lunar\Models\Collection as LunarCollection;

/**
 * Was "Homepage Collections" — only ever a list of featured collections.
 * The Clickerz Bar CTA (and any other non-collection homepage element in
 * future) is now one more row an admin can drag into that same ordered
 * list, instead of being rendered automatically in a fixed spot by
 * welcome.blade.php — hence the broader name.
 */
class HomeFeaturedCollectionsSettingsPage extends Page
{
    use InteractsWithFormActions;

    public const SETTING_KEY = 'home_featured_collections';

    public const TYPE_COLLECTION = 'collection';

    public const TYPE_CLICKERZ = 'clickerz';

    protected static ?string $cluster = WebstoreSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Homepage Elements';

    protected static ?string $title = 'Homepage Elements';

    protected static string $view = 'filament.pages.home-featured-collections-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'elements' => static::normalizedElements(WebstoreSetting::getByKey(self::SETTING_KEY, []))->toArray(),
        ]);
    }

    /**
     * The stored value has, at various points, ended up with a mix of
     * numeric-string ids and `{"collection_id": "..."}` rows (a past
     * admin form hydrated and dehydrated inconsistently) — normalize
     * whatever shape is on disk into a clean list of typed rows before
     * it ever reaches the form, rather than only fixing the display and
     * leaving the stored mess for the next load. A bare id or the old
     * `{"collection_id": ...}` shape both become a `collection` row; a
     * `{"type": "clickerz"}` row (new) is kept as-is. Duplicate
     * collection rows are dropped; duplicate Clickerz rows are allowed
     * to pass through (reorderable list, not worth guarding against).
     */
    public static function normalizedElements($stored): \Illuminate\Support\Collection
    {
        $seenCollectionIds = [];

        return collect($stored)
            ->map(function ($entry) {
                if (is_array($entry) && ($entry['type'] ?? null) === self::TYPE_CLICKERZ) {
                    return ['type' => self::TYPE_CLICKERZ];
                }

                $id = is_array($entry) ? ($entry['collection_id'] ?? null) : $entry;

                return is_numeric($id) ? ['type' => self::TYPE_COLLECTION, 'collection_id' => (int) $id] : null;
            })
            ->filter()
            ->filter(function ($entry) use (&$seenCollectionIds) {
                if ($entry['type'] !== self::TYPE_COLLECTION) {
                    return true;
                }

                if (in_array($entry['collection_id'], $seenCollectionIds, true)) {
                    return false;
                }

                $seenCollectionIds[] = $entry['collection_id'];

                return true;
            })
            ->values();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('elements')
                    ->label('Homepage elements (in order)')
                    ->helperText('A bundle collection renders as a thin call-to-action here instead of its product grid.')
                    ->schema([
                        Select::make('type')
                            ->label('Element')
                            ->options([
                                self::TYPE_COLLECTION => 'Collection',
                                self::TYPE_CLICKERZ => 'Clickerz Bar CTA',
                            ])
                            ->native(false)
                            ->live()
                            ->required(),

                        Select::make('collection_id')
                            ->label('Collection')
                            ->options(fn () => LunarCollection::all()->mapWithKeys(fn (LunarCollection $collection) => [
                                $collection->id => $collection->translateAttribute('name') ?? "Collection #{$collection->id}",
                            ]))
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('type') === self::TYPE_COLLECTION)
                            ->required(fn ($get) => $get('type') === self::TYPE_COLLECTION),
                    ])
                    ->itemLabel(fn (array $state) => match ($state['type'] ?? null) {
                        self::TYPE_CLICKERZ => '🎮 Clickerz Bar CTA',
                        self::TYPE_COLLECTION => optional(LunarCollection::find($state['collection_id'] ?? null))->translateAttribute('name') ?? 'Collection',
                        default => 'New element',
                    })
                    ->reorderable()
                    ->addActionLabel('Add element')
                    ->defaultItems(0),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        WebstoreSetting::setByKey(
            self::SETTING_KEY,
            collect($state['elements'])
                ->map(fn ($row) => $row['type'] === self::TYPE_CLICKERZ
                    ? ['type' => self::TYPE_CLICKERZ]
                    : ['type' => self::TYPE_COLLECTION, 'collection_id' => (int) $row['collection_id']])
                ->values()
                ->all()
        );

        Notification::make()
            ->title('Homepage elements saved')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save'),
        ];
    }
}

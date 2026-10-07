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

class HomeFeaturedCollectionsSettingsPage extends Page
{
    use InteractsWithFormActions;

    public const SETTING_KEY = 'home_featured_collections';

    protected static ?string $cluster = WebstoreSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Homepage Collections';

    protected static ?string $title = 'Homepage Collections';

    protected static string $view = 'filament.pages.home-featured-collections-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'collections' => static::normalizedIds(WebstoreSetting::getByKey(self::SETTING_KEY, []))
                ->map(fn ($id) => ['collection_id' => $id])
                ->toArray(),
        ]);
    }

    /**
     * The stored value has, at various points, ended up with a mix of
     * numeric-string ids and `{"collection_id": "..."}` rows (a past
     * admin form hydrated and dehydrated inconsistently) — normalize
     * whatever shape is on disk into a clean, deduplicated list of ints
     * before it ever reaches the form, rather than only fixing the
     * display and leaving the stored mess for the next load.
     */
    protected static function normalizedIds($stored): \Illuminate\Support\Collection
    {
        return collect($stored)
            ->map(fn ($entry) => is_array($entry) ? ($entry['collection_id'] ?? null) : $entry)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('collections')
                    ->label('Featured on the homepage (in order)')
                    ->helperText('A bundle collection renders as a thin call-to-action here instead of its product grid.')
                    ->schema([
                        Select::make('collection_id')
                            ->label('Collection')
                            ->options(fn () => LunarCollection::all()->mapWithKeys(fn (LunarCollection $collection) => [
                                $collection->id => $collection->translateAttribute('name') ?? "Collection #{$collection->id}",
                            ]))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->reorderable()
                    ->addActionLabel('Add collection'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        WebstoreSetting::setByKey(
            self::SETTING_KEY,
            collect($state['collections'])->pluck('collection_id')->unique()->values()->all()
        );

        Notification::make()
            ->title('Homepage collections saved')
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

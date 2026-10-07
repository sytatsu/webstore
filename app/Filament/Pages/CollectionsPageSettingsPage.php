<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\WebstoreSettings;
use App\Models\WebstoreSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Lunar\Models\Collection as LunarCollection;

class CollectionsPageSettingsPage extends Page
{
    use InteractsWithFormActions;

    public const TITLE_KEY = 'collections_page_title';

    public const DESCRIPTION_KEY = 'collections_page_description';

    public const COLLECTIONS_KEY = 'collections_page_collections';

    protected static ?string $cluster = WebstoreSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Collections Page';

    protected static ?string $title = 'Collections Page';

    protected static string $view = 'filament.pages.collections-page-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'title' => static::normalizedTranslations(WebstoreSetting::getByKey(self::TITLE_KEY)),
            'description' => static::normalizedTranslations(WebstoreSetting::getByKey(self::DESCRIPTION_KEY)),
            'collections' => collect(WebstoreSetting::getByKey(self::COLLECTIONS_KEY, []))
                ->map(fn ($slug) => ['slug' => $slug])
                ->toArray(),
        ]);
    }

    /**
     * A handful of these settings predate the per-locale shape (a plain
     * string, not `['en' => ..., 'nl' => ...]`) — treat a bare string as
     * the English value rather than feeding it straight into the
     * `title.en` / `title.nl` fields, which expect an array.
     */
    protected static function normalizedTranslations($stored): array
    {
        if (is_string($stored)) {
            return ['en' => $stored];
        }

        return $stored ?: [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Title')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title.en')
                            ->label('Title (English)')
                            ->placeholder('Collections'),
                        TextInput::make('title.nl')
                            ->label('Title (Dutch)')
                            ->helperText('Falls back to English when left empty.')
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                    ]),

                Fieldset::make('Description')
                    ->columns(2)
                    ->schema([
                        Textarea::make('description.en')
                            ->label('Description (English)')
                            ->rows(3),
                        Textarea::make('description.nl')
                            ->label('Description (Dutch)')
                            ->rows(3)
                            ->helperText('Falls back to English when left empty.')
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                    ]),

                Repeater::make('collections')
                    ->label('Collections shown (in order)')
                    ->schema([
                        Select::make('slug')
                            ->label('Collection')
                            ->options(fn () => LunarCollection::query()->with('defaultUrl')->get()
                                ->filter(fn (LunarCollection $collection) => $collection->defaultUrl?->slug !== null)
                                ->mapWithKeys(fn (LunarCollection $collection) => [
                                    $collection->defaultUrl->slug => $collection->translateAttribute('name') ?? "Collection #{$collection->id}",
                                ]))
                            ->searchable()
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

        WebstoreSetting::setByKey(self::TITLE_KEY, array_filter($state['title'] ?? []));
        WebstoreSetting::setByKey(self::DESCRIPTION_KEY, array_filter($state['description'] ?? []));
        WebstoreSetting::setByKey(self::COLLECTIONS_KEY, collect($state['collections'])->pluck('slug')->values()->all());

        Notification::make()
            ->title('Collections page saved')
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

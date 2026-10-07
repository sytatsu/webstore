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
use Lunar\Models\CollectionGroup;

class NavigationSettingsPage extends Page
{
    use InteractsWithFormActions;

    public const GROUPS_KEY = 'navigation_collection_groups';

    public const FDM_PRINTING_KEY = 'navigation_fdm_printing_handles';

    public const TOP_LEVEL_ORDER_KEY = 'navigation_top_level_order';

    /**
     * The nav bar's top-level entries, in the order they've always
     * rendered — used both as the default when nothing is stored yet,
     * and (via TOP_LEVEL_TYPES) as the whitelist a stored value is
     * filtered against, so a type removed from the codebase or a typo
     * can't leave a broken entry in the nav.
     */
    public const DEFAULT_TOP_LEVEL_ORDER = ['collections', 'clickerz', 'fdm_printing', 'services'];

    public const TOP_LEVEL_TYPES = [
        'collections' => 'Collections dropdown',
        'clickerz' => 'Clickerz Bar link',
        'fdm_printing' => 'FDM Printing dropdown',
        'services' => 'Services dropdown',
    ];

    protected static ?string $cluster = WebstoreSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $navigationLabel = 'Navigation';

    protected static ?string $title = 'Navigation';

    protected static string $view = 'filament.pages.navigation-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'top_level_order' => static::normalizedTopLevelOrder(
                WebstoreSetting::getByKey(self::TOP_LEVEL_ORDER_KEY, self::DEFAULT_TOP_LEVEL_ORDER)
            )->map(fn ($type) => ['type' => $type])->toArray(),
            'groups' => collect(WebstoreSetting::getByKey(self::GROUPS_KEY, ['printed']))
                ->map(fn ($handle) => ['handle' => $handle])
                ->toArray(),
            'fdm_printing_collections' => collect(WebstoreSetting::getByKey(self::FDM_PRINTING_KEY, ['polymaker']))
                ->map(fn ($slug) => ['slug' => $slug])
                ->toArray(),
        ]);
    }

    /**
     * Filters out anything that isn't a known type (a stale value from
     * before a type existed, a typo written directly to the database)
     * rather than letting it render as a broken nav entry — but doesn't
     * silently re-add a type the admin deliberately removed, so the
     * default is only used when nothing is stored at all (the
     * `getByKey` default above), not merged in here.
     */
    public static function normalizedTopLevelOrder($stored): \Illuminate\Support\Collection
    {
        return collect($stored)->filter(fn ($type) => array_key_exists($type, self::TOP_LEVEL_TYPES))->values();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('top_level_order')
                    ->label('Nav bar — top-level order')
                    ->helperText('What shows in the main nav bar and in what order. The Clickerz Bar link only actually appears while the Bar Builder is enabled (Bar Builder → Settings); the FDM Printing dropdown only appears once it has at least one collection below. Remove an entry here to hide it regardless.')
                    ->schema([
                        Select::make('type')
                            ->label('Item')
                            ->options(self::TOP_LEVEL_TYPES)
                            ->native(false)
                            ->required(),
                    ])
                    ->itemLabel(fn (array $state) => self::TOP_LEVEL_TYPES[$state['type'] ?? null] ?? 'New item')
                    ->reorderable()
                    ->addActionLabel('Add item'),

                Repeater::make('groups')
                    ->label('Collections dropdown — groups shown (in order)')
                    ->helperText('Every root collection belonging to a listed group appears in the "Collections" dropdown, groups shown in the order listed here.')
                    ->schema([
                        Select::make('handle')
                            ->label('Collection group')
                            ->options(fn () => CollectionGroup::all()->mapWithKeys(fn (CollectionGroup $group) => [$group->handle => $group->name]))
                            ->searchable()
                            ->required(),
                    ])
                    ->reorderable()
                    ->addActionLabel('Add group')
                    ->minItems(1),

                Repeater::make('fdm_printing_collections')
                    ->label('FDM Printing dropdown — collections shown (in order)')
                    ->helperText('The "FDM Printing" dropdown only appears once at least one collection is listed here. Individual collections, not whole groups.')
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

        WebstoreSetting::setByKey(
            self::TOP_LEVEL_ORDER_KEY,
            collect($state['top_level_order'])->pluck('type')->unique()->values()->all()
        );
        WebstoreSetting::setByKey(self::GROUPS_KEY, collect($state['groups'])->pluck('handle')->values()->all());
        WebstoreSetting::setByKey(self::FDM_PRINTING_KEY, collect($state['fdm_printing_collections'])->pluck('slug')->values()->all());

        Notification::make()
            ->title('Navigation saved')
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

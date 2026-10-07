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
            'groups' => collect(WebstoreSetting::getByKey(self::GROUPS_KEY, ['printed']))
                ->map(fn ($handle) => ['handle' => $handle])
                ->toArray(),
            'fdm_printing_collections' => collect(WebstoreSetting::getByKey(self::FDM_PRINTING_KEY, ['polymaker']))
                ->map(fn ($slug) => ['slug' => $slug])
                ->toArray(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

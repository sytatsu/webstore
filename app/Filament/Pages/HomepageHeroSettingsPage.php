<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\WebstoreSettings;
use App\Models\WebstoreSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class HomepageHeroSettingsPage extends Page
{
    use InteractsWithFormActions;

    public const SETTING_KEY = 'homepage_hero';

    public const DEFAULT_HERO = 'clickerz';

    protected static ?string $cluster = WebstoreSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Homepage Hero';

    protected static ?string $title = 'Homepage Hero';

    protected static string $view = 'filament.pages.homepage-hero-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'hero' => static::stored(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('hero')
                    ->label('Homepage hero')
                    ->options([
                        'main' => 'Sytatsu general hero',
                        'clickerz' => 'Clickerz Bar Builder hero',
                    ])
                    ->helperText('If the Clickerz Bar hero is selected while the Bar Builder is disabled, the general hero is shown instead.')
                    ->native(false)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        WebstoreSetting::setByKey(self::SETTING_KEY, $state['hero']);

        Notification::make()
            ->title('Homepage hero saved')
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

    protected static function stored(): string
    {
        return WebstoreSetting::getByKey(self::SETTING_KEY, self::DEFAULT_HERO);
    }

    /**
     * The hero that should actually be rendered, accounting for the
     * Clickerz hero not being available when the Bar Builder is disabled.
     */
    public static function current(): string
    {
        if (static::stored() === 'clickerz' && ! BarBuilderSettingsPage::isEnabled()) {
            return 'main';
        }

        return static::stored();
    }
}

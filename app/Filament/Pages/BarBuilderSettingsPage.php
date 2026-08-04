<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\BarBuilder;
use App\Models\WebstoreSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class BarBuilderSettingsPage extends Page
{
    use InteractsWithFormActions;

    public const SETTING_KEY = 'bar_builder_enabled';

    protected static ?string $cluster = BarBuilder::class;

    protected static ?string $navigationIcon = 'heroicon-o-power';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Bar Builder Settings';

    protected static string $view = 'filament.pages.bar-builder-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'enabled' => static::isEnabled(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('enabled')
                    ->label('Bar Builder enabled')
                    ->helperText('When disabled, the /clickerz/builder page 404s, the builder no longer appears on the Clickerz Bar product page, and its navigation link and homepage promo are hidden. Existing cart items and past orders are unaffected.')
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        WebstoreSetting::setByKey(self::SETTING_KEY, $state['enabled']);

        Notification::make()
            ->title('Bar Builder settings saved')
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

    public static function isEnabled(): bool
    {
        return (bool) WebstoreSetting::getByKey(self::SETTING_KEY, true);
    }
}

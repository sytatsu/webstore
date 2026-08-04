<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\BarBuilder;
use App\Models\BarBuilderBaseColor;
use App\Models\BarBuilderCapCombo;
use App\Models\BarBuilderIcon;
use App\Models\WebstoreSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class BarBuilderDefaultArrangementPage extends Page
{
    use InteractsWithFormActions;

    public const SETTING_KEY = 'bar_builder_default_arrangement';

    protected static ?string $cluster = BarBuilder::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Default Arrangement';

    protected static ?string $title = 'Default Arrangement';

    protected static string $view = 'filament.pages.bar-builder-default-arrangement';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $stored = WebstoreSetting::getByKey(self::SETTING_KEY, []);

        $this->form->fill([
            'word' => $stored['word'] ?? 'CLICKERZ',
            'base_color_id' => $stored['base_color_id'] ?? null,
            'caps' => $stored['caps'] ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('word')
                    ->label('Default word')
                    ->helperText('The text the builder opens with. Use a space for any position that should start with an icon instead of a letter.')
                    ->maxLength(10)
                    ->required(),
                Select::make('base_color_id')
                    ->label('Default base colour')
                    ->options(fn () => BarBuilderBaseColor::query()->ordered()->enabled()->get()
                        ->mapWithKeys(fn (BarBuilderBaseColor $color) => [$color->id => $color->translate('name')]))
                    ->searchable()
                    ->native(false),
                Repeater::make('caps')
                    ->label('Default cap colours')
                    ->helperText('One entry per cap position, in order. Positions beyond this list fall back to a random colour combination, matching what customers see today.')
                    ->schema([
                        Select::make('combo_id')
                            ->label('Colour combination')
                            ->options(fn () => BarBuilderCapCombo::query()->ordered()->enabled()->get()
                                ->mapWithKeys(fn (BarBuilderCapCombo $combo) => [$combo->id => $combo->translate('name')]))
                            ->searchable()
                            ->native(false)
                            ->required(),
                        Select::make('icon_id')
                            ->label('Icon (optional)')
                            ->options(fn () => BarBuilderIcon::query()->ordered()->enabled()->get()
                                ->mapWithKeys(fn (BarBuilderIcon $icon) => [$icon->id => $icon->translate('name')]))
                            ->searchable()
                            ->native(false),
                    ])
                    ->columns(2)
                    ->reorderable()
                    ->maxItems(10)
                    ->addActionLabel('Add cap position'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        WebstoreSetting::setByKey(self::SETTING_KEY, [
            'word' => $state['word'],
            'base_color_id' => $state['base_color_id'],
            'caps' => $state['caps'],
        ]);

        Notification::make()
            ->title('Default arrangement saved')
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

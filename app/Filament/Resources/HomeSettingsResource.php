<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\WebstoreSettings;
use App\Filament\Resources\HomeSettingsResource\Pages;
use App\Filament\Resources\HomeSettingsResource\RelationManagers;
use App\Models\HomeSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Lunar\Admin\Support\Forms\Components\TranslatedText;
use Lunar\Models\Collection;

class HomeSettingsResource extends Resource
{
    protected static ?string $cluster = WebstoreSettings::class;
    protected static ?string $model = HomeSettings::class;
    protected static ?string $navigationLabel = 'Welcome Page';
    protected static ?string $pluralLabel = 'Welcome Page Settings';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->default('Default'),
                        Toggle::make('is_active')
                            ->label('Set as active')
                            ->helperText('Only one setting can be active at a time.')
                            ->afterStateUpdated(function ($state, $record) {
                                if ($state) {
                                    HomeSettings::where('id', '!=', $record?->id)->update(['is_active' => false]);
                                }
                            })
                            ->default(false),
                    ])->columns(2),

                Section::make('Hero Content')
                    ->schema([
                        TranslatedText::make('title')
                            ->placeholder('e.g. Welcome to our Store'),
                        TranslatedText::make('sub_title')
                            ->placeholder('e.g. Find the best products here'),
                    ]),

                Section::make('Featured Collections')
                    ->schema([
                        Repeater::make('homeCollections')
                            ->relationship()
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
                            ->orderColumn('position')
                            ->defaultItems(0)
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('title'),
                ToggleColumn::make('is_active'),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHomeSettings::route('/'),
        ];
    }
}

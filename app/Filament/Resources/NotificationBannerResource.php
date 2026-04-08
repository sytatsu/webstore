<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\WebstoreSettings;
use App\Filament\Resources\NotificationBannerResource\Pages;
use App\Filament\Resources\NotificationBannerResource\RelationManagers;
use App\Models\NotificationBanner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Lunar\Admin\Support\Forms\Components\TranslatedText;

class NotificationBannerResource extends Resource
{
    protected static ?string $cluster = WebstoreSettings::class;

    protected static ?string $model = NotificationBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General')
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('e.g. Summer Sale Banner')
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Only one banner can be active at a time.')
                            ->afterStateUpdated(function ($state, $record) {
                                if ($state) {
                                    NotificationBanner::where('id', '!=', $record?->id)->update(['is_active' => false]);
                                }
                            })
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Banner Content')
                    ->schema([
                        TranslatedText::make('banner_text')
                            ->label('Banner Text')
                            ->required()
                            ->placeholder('e.g. 20% off all items this weekend!'),
                        TextInput::make('banner_icon')
                            ->label('Banner Icon')
                            ->placeholder('heroicon-o-megaphone')
                            ->helperText('Use Heroicons (e.g. heroicon-o-sparkles)'),
                        TextInput::make('banner_url')
                            ->label('Banner Link (optional)')
                            ->placeholder('e.g. /collections/sale'),
                        DateTimePicker::make('banner_start_at')
                            ->label('Start Time'),
                        DateTimePicker::make('banner_end_at')
                            ->label('End Time'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('banner_text')
                    ->limit(50),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->afterStateUpdated(function ($state, $record) {
                        if ($state) {
                            NotificationBanner::where('id', '!=', $record?->id)->update(['is_active' => false]);
                        }
                    }),
                TextColumn::make('banner_start_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('banner_end_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ManageNotificationBanners::route('/'),
        ];
    }
}

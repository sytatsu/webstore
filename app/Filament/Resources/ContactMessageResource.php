<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ContactMessageResource\Pages\ManageContactMessages;
use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Contact messages';

    protected static ?string $pluralModelLabel = 'Contact messages';

    protected static ?string $modelLabel = 'Contact message';

    protected const SERVICE_TYPES = [
        'contact' => 'Contact',
        'custom_print' => 'Custom print',
        'maintenance' => 'Maintenance',
        'repair' => 'Repair',
    ];

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')->copyable(),
                TextEntry::make('phone')->placeholder('-'),
                TextEntry::make('service_type')
                    ->label('Form')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::SERVICE_TYPES[$state] ?? $state),
                TextEntry::make('details')->label('Message')->columnSpanFull()->prose(),
                IconEntry::make('is_spam')->label('Spam')->boolean(),
                TextEntry::make('spam_reason')->label('Spam reason')->placeholder('-'),
                TextEntry::make('ip_address')->label('IP address')->placeholder('-'),
                TextEntry::make('created_at')->label('Received')->dateTime(),
                TextEntry::make('user_agent')->label('User agent')->placeholder('-')->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('service_type')
                    ->label('Form')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::SERVICE_TYPES[$state] ?? $state),
                TextColumn::make('details')
                    ->label('Message')
                    ->limit(60)
                    ->searchable(),
                IconColumn::make('is_spam')
                    ->label('Spam')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),
                TextColumn::make('spam_reason')
                    ->label('Reason')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_spam')
                    ->label('Spam')
                    ->trueLabel('Only spam')
                    ->falseLabel('Only real messages')
                    ->placeholder('All')
                    ->default(false),
                SelectFilter::make('service_type')
                    ->label('Form')
                    ->options(self::SERVICE_TYPES),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markSpam')
                    ->label('Mark as spam')
                    ->icon('heroicon-m-no-symbol')
                    ->color('danger')
                    ->visible(fn (ContactMessage $record): bool => ! $record->is_spam)
                    ->action(fn (ContactMessage $record) => $record->update(['is_spam' => true, 'spam_reason' => 'marked manually'])),
                Action::make('markNotSpam')
                    ->label('Not spam')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (ContactMessage $record): bool => $record->is_spam)
                    ->action(fn (ContactMessage $record) => $record->update(['is_spam' => false, 'spam_reason' => null])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markSpam')
                        ->label('Mark as spam')
                        ->icon('heroicon-m-no-symbol')
                        ->color('danger')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each->update(['is_spam' => true, 'spam_reason' => 'marked manually'])),
                    BulkAction::make('markNotSpam')
                        ->label('Not spam')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each->update(['is_spam' => false, 'spam_reason' => null])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContactMessages::route('/'),
        ];
    }
}

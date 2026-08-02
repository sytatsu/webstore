<?php

namespace App\Filament\Resources\Concerns;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasTranslatableName
{
    protected static function nameFormFieldset(string $placeholderEn = '', string $placeholderNl = ''): Fieldset
    {
        return Fieldset::make('Name')
            ->columnSpanFull()
            ->columns(2)
            ->schema([
                TextInput::make('name.en')
                    ->label('Name (English)')
                    ->placeholder($placeholderEn)
                    ->required(),
                TextInput::make('name.nl')
                    ->label('Name (Dutch)')
                    ->placeholder($placeholderNl)
                    ->helperText('Falls back to English when left empty.')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
            ]);
    }

    protected static function nameTableColumn(): TextColumn
    {
        return TextColumn::make('name')
            ->label('Name')
            ->getStateUsing(fn (Model $record) => $record->translate('name'))
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query->where(function (Builder $query) use ($search) {
                    $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.nl')) LIKE ?", ["%{$search}%"]);
                });
            })
            ->sortable(query: fn (Builder $query, string $direction) => $query
                ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) {$direction}"));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasTranslations;

class BarBuilderCapCombo extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'cap_hex',
        'text_hex',
        'sort_order',
        'enabled',
    ];

    protected $casts = [
        'name' => 'array',
        'enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }
}

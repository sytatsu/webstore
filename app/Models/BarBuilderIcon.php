<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasTranslations;

class BarBuilderIcon extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'svg_paths',
        'cx',
        'cy',
        'scale',
        'sort_order',
        'enabled',
    ];

    protected $casts = [
        'name' => 'array',
        'svg_paths' => 'array',
        'cx' => 'float',
        'cy' => 'float',
        'scale' => 'float',
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

    public function getPathAttribute(): string
    {
        return implode(' ', $this->svg_paths ?? []);
    }
}

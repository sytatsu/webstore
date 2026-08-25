<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasTranslations;

class PickupLocation extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'address_line_1',
        'address_line_2',
        'postcode',
        'city',
        'country',
        'availability_note',
        'price',
        'enabled',
        'sort_order',
    ];

    protected $casts = [
        'availability_note' => 'array',
        'price' => 'integer',
        'enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            trim("{$this->postcode} {$this->city}"),
            $this->country,
        ])->filter()->implode(', ');
    }
}

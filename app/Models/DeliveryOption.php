<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShippingCarrierEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DeliveryOption extends Model
{
    protected $fillable = [
        'carrier',
        'name',
        'description',
        'identifier',
        'price',
        'free_shipping',
        'enabled',
        'sort_order',
    ];

    protected $casts = [
        'carrier' => ShippingCarrierEnum::class,
        'price' => 'integer',
        'free_shipping' => 'boolean',
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

    public function scopeForCarrier(Builder $query, ShippingCarrierEnum $carrier): Builder
    {
        return $query->where('carrier', $carrier);
    }
}

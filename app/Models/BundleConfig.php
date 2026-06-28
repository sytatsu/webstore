<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Lunar\Base\Traits\HasTranslations;
use Lunar\Models\Collection;

class BundleConfig extends Model
{
    use HasTranslations;

    protected $fillable = [
        'collection_id',
        'bundle_name',
        'enabled',
        'discount_tiers',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * @return array
     */
    public function getBundleNameAttribute($value)
    {
        return json_decode($value, true) ?: [];
    }

    /**
     * @param array $value
     */
    public function setBundleNameAttribute($value)
    {
        $this->attributes['bundle_name'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * @return array
     */
    public function getDiscountTiersAttribute($value)
    {
        return json_decode($value, true) ?: [];
    }

    /**
     * @param array $value
     */
    public function setDiscountTiersAttribute($value)
    {
        $this->attributes['discount_tiers'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getTranslatedName(): string
    {
        return (string) $this->translate('bundle_name') ?: '';
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function getActiveTier(int $qty): ?array
    {
        $tiers = collect($this->discount_tiers ?? [])
            ->sortByDesc('min_quantity');

        foreach ($tiers as $tier) {
            if ($qty >= (int) $tier['min_quantity']) {
                return $tier;
            }
        }

        return null;
    }

    public function getNextTier(int $qty): ?array
    {
        return collect($this->discount_tiers ?? [])
            ->sortBy('min_quantity')
            ->first(fn(array $tier) => (int) $tier['min_quantity'] > $qty);
    }
}

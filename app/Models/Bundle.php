<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Lunar\Base\Traits\HasTranslations;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\Price;
use Lunar\Models\ProductVariant;

/**
 * Ties one "buy N, pay less per item" bundle configuration to a collection.
 *
 * Pricing is deliberately NOT stored on this model. The tiers an admin
 * configures are persisted as real Lunar `Price` rows (one per tier,
 * keyed by `min_quantity`) on a single hidden product variant
 * (`$this->variant`), so that adding a bundle to the cart can lean on
 * Lunar's own quantity price-break resolution (`PricingManager::get()`)
 * instead of a custom discount pipeline. See `App\Services\BundleService`
 * for how that variant is created/kept in sync, and `docs/bundles/README.md`
 * for the full rationale.
 */
class Bundle extends Model
{
    use HasTranslations;

    protected $fillable = [
        'collection_id',
        'product_variant_id',
        'name',
        'enabled',
        'max_items',
    ];

    protected $casts = [
        'name' => 'array',
        'enabled' => 'boolean',
        'max_items' => 'integer',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(LunarCollection::class, 'collection_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public function getTranslatedName(): string
    {
        return (string) $this->translate('name') ?: '';
    }

    /**
     * The configured tiers, cheapest-quantity-first, straight from the
     * backing variant's own Price rows (the source of truth).
     *
     * @return Collection<int, Price>
     */
    public function tiers(): Collection
    {
        if (!$this->variant) {
            return collect();
        }

        return $this->variant->prices
            ->where('customer_group_id', null)
            ->sortBy('min_quantity')
            ->values();
    }

    /**
     * The tier that would apply for a given quantity: the highest
     * `min_quantity` tier that is `<= $quantity`. Mirrors
     * `Lunar\Managers\PricingManager::get()`'s own price-break matching so
     * the storefront preview (before anything is added to the cart) always
     * agrees with what checkout will actually charge.
     */
    public function priceForQuantity(int $quantity): ?Price
    {
        return $this->tiers()
            ->filter(fn (Price $price) => $price->min_quantity <= $quantity)
            ->sortByDesc('min_quantity')
            ->first();
    }

    /**
     * The next tier (if any) a customer could reach by adding more items,
     * for "pick 1 more to unlock €1.25 each" style messaging.
     */
    public function nextTier(int $quantity): ?Price
    {
        return $this->tiers()
            ->first(fn (Price $price) => $price->min_quantity > $quantity);
    }
}

<?php

namespace App\Services;

use App\Support\PrintOnDemand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Lunar\Models\ProductVariant;

class InventoryService
{
    public const FIELDS = ['stock', 'backorder'];

    public const MODE_SET = 'set';

    public const MODE_ADD = 'add';

    public const MODE_SUBTRACT = 'subtract';

    public const PURCHASABILITY_OPTIONS = [
        'in_stock' => 'Only when in stock',
        'in_stock_or_on_backorder' => 'In stock or on backorder',
        PrintOnDemand::PURCHASABILITY => 'In stock or print on demand',
        'always' => 'Always',
    ];

    /**
     * Every variant of every product belonging to one of the given brands.
     */
    public function variantsForBrands(array $brandIds): Builder
    {
        return ProductVariant::query()
            ->whereHas('product', fn (Builder $query) => $query->whereIn('brand_id', $brandIds));
    }

    /**
     * Sets, raises or lowers `stock` / `backorder` on the given variants.
     * Lowering never goes below zero. Variants are saved one by one (not a
     * mass update) so Lunar's activity log still records each change.
     *
     * @param  Builder|Collection<int, ProductVariant>  $variants
     * @return int number of variants whose value actually changed
     */
    public function adjust(Builder|Collection $variants, string $field, string $mode, int $quantity): int
    {
        if (! in_array($field, self::FIELDS, true)) {
            throw new InvalidArgumentException("Cannot bulk update inventory field [{$field}].");
        }

        $quantity = max(0, $quantity);

        return $this->each($variants, function (ProductVariant $variant) use ($field, $mode, $quantity) {
            $current = (int) $variant->{$field};

            $variant->{$field} = match ($mode) {
                self::MODE_SET => $quantity,
                self::MODE_ADD => $current + $quantity,
                self::MODE_SUBTRACT => max(0, $current - $quantity),
                default => throw new InvalidArgumentException("Unknown inventory update mode [{$mode}]."),
            };
        });
    }

    /**
     * @param  Builder|Collection<int, ProductVariant>  $variants
     * @return int number of variants whose value actually changed
     */
    public function setPurchasability(Builder|Collection $variants, string $purchasable): int
    {
        if (! array_key_exists($purchasable, self::PURCHASABILITY_OPTIONS)) {
            throw new InvalidArgumentException("Unknown purchasability [{$purchasable}].");
        }

        return $this->each($variants, function (ProductVariant $variant) use ($purchasable) {
            $variant->purchasable = $purchasable;
        });
    }

    /**
     * @param  Builder|Collection<int, ProductVariant>  $variants
     */
    protected function each(Builder|Collection $variants, callable $change): int
    {
        $updated = 0;

        $apply = function (ProductVariant $variant) use ($change, &$updated) {
            $change($variant);

            if ($variant->isDirty()) {
                $variant->save();
                $updated++;
            }
        };

        if ($variants instanceof Builder) {
            $variants->chunkById(200, fn (Collection $chunk) => $chunk->each($apply));
        } else {
            $variants->each($apply);
        }

        return $updated;
    }
}

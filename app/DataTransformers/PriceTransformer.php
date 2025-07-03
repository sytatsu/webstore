<?php

namespace App\DataTransformers;

use Illuminate\Support\Collection;

class PriceTransformer
{

    /**
     * @param \Illuminate\Support\Collection<\Lunar\Models\Price> $priceCollection
     */
    public static function rangeString(Collection $priceCollection): string
    {
        $priceCollection = $priceCollection->unique('price');

        if ($priceCollection->count() <= 1) {
            return $priceCollection->first()?->price->formatted() ?? 'N/A';
        }

        return sprintf(
            '%s %s',
            __('From'),
            $priceCollection->sortBy('price')->first()->price->formatted()
        );
    }
}

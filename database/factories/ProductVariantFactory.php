<?php

namespace Database\Factories;

use App\DataObject\Currency;
use App\Enums\AvailabilityEnum;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{

    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_uuid' => fn () => Product::factory()->create(),
            'variant_uuid' => fn () => Variant::factory()->create(),

            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => Currency::from(($this->faker->randomNumber(2) * 100)),
            'sku' => $this->faker->uuid,
        ];
    }
}

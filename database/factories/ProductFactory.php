<?php

namespace Database\Factories;

use App\Enums\ProductTypeEnum;
use App\Enums\ProductVariantType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,

            'product_type' => $this->faker->randomElement(ProductTypeEnum::cases()),
            'product_variant_type' => $this->faker->randomElement(ProductVariantType::cases()),

            'has_multiple_variants' => false,

            'brand_uuid' => fn () => Brand::factory()->create(),
            'category_uuid' => fn () => Category::factory()->create(),

            'discontinued_at' => null,
        ];
    }
}

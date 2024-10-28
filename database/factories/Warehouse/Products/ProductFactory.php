<?php

namespace Database\Factories\Warehouse\Products;

use App\Enums\ProductTypeEnum;
use App\Enums\ProductVariantType;
use App\Models\Types\Brand;
use App\Models\Types\Category;
use App\Models\Warehouse\Products\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse\Products\Product>
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

            'type_brand_uuid' => Brand::factory()->create(),
            'type_category_uuid' => Category::factory()->create(),

            'discontinued_at' => null,
        ];
    }
}

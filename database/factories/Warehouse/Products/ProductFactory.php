<?php

namespace Database\Factories\Warehouse\Products;

use App\Enums\ProductTypeEnum;
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
            'type_brand_uuid' => Brand::factory()->create(),
            'type_category_uuid' => Category::factory()->create(),
            'type' => $this->faker->randomElement(ProductTypeEnum::cases())->name,
            'discontinued_at' => null,
        ];
    }
}

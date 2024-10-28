<?php

namespace Database\Factories\Warehouse\Products;

use App\Enums\AvailabilityEnum;
use App\Enums\ProductVariantType;
use App\Models\Types\Variant;
use App\Models\Warehouse\Products\Product;
use App\Models\Warehouse\Products\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse\Products\ProductVariant>
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
            'warehouse_product_uuid' => Product::factory()->create(),
            'type_variant_uuid' => Variant::factory()->create(),
            'product_variant_type' => $this->faker->randomElement(ProductVariantType::cases())->name,
            'price' => ($this->faker->randomNumber(2) * 100),
            'availability_type' => $this->faker->randomElement(AvailabilityEnum::cases())->name,
            'availability_quantity' => $this->faker->randomNumber(1),
        ];
    }
}

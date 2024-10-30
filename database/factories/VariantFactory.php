<?php

namespace Database\Factories;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory
{
    protected $model = Variant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->colorName,
            'description' => $this->faker->paragraph,
            'parent_variant_uuid' => null,
        ];
    }

    public function withParent(): Factory|CategoryFactory
    {
        return $this->state(fn (array $attributes) => [
            'parent_variant_uuid' => fn () => Variant::factory()->create(),
        ]);
    }
}

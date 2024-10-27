<?php

namespace Database\Factories\Types;

use App\Models\Types\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Types\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'parent_category_uuid' => null,
        ];
    }

    public function withParent(): Factory|CategoryFactory
    {
        return $this->state(fn (array $attributes) => [
            'parent_category_uuid' => Category::factory()->create(),
        ]);
    }
}

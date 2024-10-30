<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
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
            'parent_category_uuid' => fn () => Category::factory()->create(),
        ]);
    }
}

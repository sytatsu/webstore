<?php

namespace App\Repositories\Warehouse;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function all(?array $withRelations = []): Collection
    {
        return Category::with(relations: $withRelations ?? [])->get();
    }

    public function find(string $uuid): ?Category
    {
        return Category::find($uuid) ?? null;
    }

    public function findByName(string $name): ?Category
    {
        return Category::where('name', $name)->first();
    }

    public function fill(Category $category, string $name, string $description): Category
    {
        $category->name = $name;
        $category->description = $description;

        return $category;
    }

    public function save(Category $category): Category
    {
        $category->save();
        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}

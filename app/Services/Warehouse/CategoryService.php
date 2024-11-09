<?php

namespace App\Services\Warehouse;

use App\Models\Category;
use App\Repositories\Warehouse\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
        //
    }

    public function allList(): Collection
    {
        return $this->categoryRepository->all(withRelations: ['products']);
    }

    public function findByUuid(string $uuid): ?Category
    {
        return $this->categoryRepository->find(uuid: $uuid);
    }

    public function new(): Category
    {
        return new Category();
    }

    public function store(array $data, ?Category $category = null): Category
    {
        if ($category === null) {
            $category = $this->new();
        }

        $this->categoryRepository->fill(category: $category, name: $data['name'], description: $data['description']);
        $this->categoryRepository->save(category: $category);

        return $category;
    }

    public function delete(string $uuid): void
    {
        $category = $this->categoryRepository->find(uuid: $uuid);
        $this->categoryRepository->delete(category: $category);
    }
}

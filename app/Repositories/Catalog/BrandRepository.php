<?php

namespace App\Repositories\Catalog;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;


class BrandRepository
{
    public function all(?array $withRelations = []): Collection
    {
        return Brand::with(relations: $withRelations ?? [])->get();
    }

    public function find(string $uuid): ?Brand
    {
        return Brand::find($uuid) ?? null;
    }

    public function findByName(string $name): ?Brand
    {
        return Brand::where('name', $name)->first();
    }

    public function fill(Brand $brand, string $name, ?string $description): Brand
    {
        $brand->name = $name;
        $brand->description = $description;

        return $brand;
    }

    public function save(Brand $brand): Brand
    {
        $brand->save();
        return $brand;
    }

    public function delete(Brand $brand): void
    {
        $brand->delete();
    }
}

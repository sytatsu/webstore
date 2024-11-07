<?php

namespace App\Repositories\Warehouse;

use App\Models\Brand;

class BrandRepository
{
    public function find(string $uuid): Brand
    {
        return Brand::findOrFail($uuid);
    }

    public function fill(Brand $brand, string $name, string $description): Brand
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
}

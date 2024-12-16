<?php

namespace App\Services\Warehouse;

use App\Models\Brand;
use App\Repositories\Warehouse\BrandRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;

class BrandService
{
    public function __construct(
        private readonly BrandRepository $brandRepository,
    ) {
        //
    }

    public function getBrands(): EloquentCollection
    {
        return $this->brandRepository->all(withRelations: ['products']);
    }

    public function getBrandList(): SupportCollection
    {
        return $this->brandRepository->all()->pluck('name', 'uuid');
    }

    public function findByUuid(string $uuid): ?Brand
    {
        return $this->brandRepository->find(uuid: $uuid);
    }

    public function findByNameOrCreate(string $name): Brand
    {
        return $this->brandRepository->findByName(name: $name)
            ?? $this->store(data: ['name' => $name]);
    }


    public function new(): Brand
    {
        return new Brand();
    }

    public function store(array $data, ?Brand $brand = null): Brand
    {
        if ($brand === null) {
            $brand = $this->new();
        }

        $this->brandRepository->fill(brand: $brand, name: $data['name'], description: $data['description'] ?? null);
        $this->brandRepository->save(brand: $brand);

        return $brand;
    }

    public function delete(string $uuid): void
    {
        $brand = $this->brandRepository->find($uuid);
        $this->brandRepository->delete($brand);
    }
}

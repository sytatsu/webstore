<?php

namespace App\Services\Warehouse;

use App\Models\Brand;
use App\Repositories\Warehouse\BrandRepository;
use Illuminate\Database\Eloquent\Collection;

class BrandService
{
    public function __construct(
        private readonly BrandRepository $brandRepository,
    ) {
        //
    }

    public function getBrandList(): Collection
    {
        return $this->brandRepository->all(withRelations: ['products']);
    }

    public function findByUuid(string $uuid): ?Brand
    {
        return $this->brandRepository->find(uuid: $uuid);
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

        $this->brandRepository->fill(brand: $brand, name: $data['name'], description: $data['description'],);
        $this->brandRepository->save(brand: $brand);

        return $brand;
    }

    public function delete(string $uuid): void
    {
        $brand = $this->brandRepository->find($uuid);
        $this->brandRepository->delete($brand);
    }
}

<?php

namespace App\Services\Warehouse;

use App\Models\Brand;
use App\Repositories\Warehouse\BrandRepository;

class BrandService
{
    public function __construct(
        private BrandRepository $brandRepository,
    ) {
        //
    }

    public function findByUuid(string $uuid): ?Brand
    {
        return $this->brandRepository->find($uuid);
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

        $this->brandRepository->fill($brand, $data['name'], $data['description'],);
        $this->brandRepository->save($brand);

        return $brand;
    }
}

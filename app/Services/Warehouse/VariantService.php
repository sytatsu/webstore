<?php

namespace App\Services\Warehouse;

use App\Models\Variant;
use App\Repositories\Warehouse\VariantRepository;
use Illuminate\Database\Eloquent\Collection;

class VariantService
{
    public function __construct(
        private readonly VariantRepository $variantRepository,
    ) {
        //
    }

    public function getVariantList(): Collection
    {
        return $this->variantRepository->all(withRelations: ['productVariants']);
    }

    public function findByUuid(string $uuid): ?Variant
    {
        return $this->variantRepository->find(uuid: $uuid);
    }

    public function new(): Variant
    {
        return new Variant();
    }

    public function store(array $data, ?Variant $variant = null): Variant
    {
        if ($variant === null) {
            $variant = $this->new();
        }

        $this->variantRepository->fill(variant: $variant, name: $data['name'], description: $data['description'],);
        $this->variantRepository->save(variant: $variant);

        return $variant;
    }

    public function delete(string $uuid): void
    {
        $variant = $this->variantRepository->find($uuid);
        $this->variantRepository->delete($variant);
    }
}

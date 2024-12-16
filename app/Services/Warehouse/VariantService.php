<?php

namespace App\Services\Warehouse;

use App\Models\Variant;
use App\Repositories\Warehouse\VariantRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;

class VariantService
{
    public function __construct(
        private readonly VariantRepository $variantRepository,
    ) {
        //
    }

    public function getVariants(): EloquentCollection
    {
        return $this->variantRepository->all(withRelations: ['productVariants']);
    }

    public function getVariantList(): SupportCollection
    {
        return $this->variantRepository->all()->pluck('name', 'uuid');
    }

    public function findByUuid(string $uuid): ?Variant
    {
        return $this->variantRepository->find(uuid: $uuid);
    }

    public function findByNameOrCreate(string $name, ?Variant $parentVariant = null): Variant
    {
        return $this->variantRepository->findByName(name: $name)
            ?? $this->store(data: ['name' => $name, 'parent_variant' => $parentVariant]);
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

        $this->variantRepository->fill(variant: $variant, name: $data['name'], description: $data['description'] ?? null, parentVariant: $data['parent_variant']);
        $this->variantRepository->save(variant: $variant);

        return $variant;
    }

    public function delete(string $uuid): void
    {
        $variant = $this->variantRepository->find($uuid);
        $this->variantRepository->delete($variant);
    }
}

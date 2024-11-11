<?php

namespace App\Repositories\Warehouse;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Collection;

class VariantRepository
{
    public function all(?array $withRelations): Collection
    {
        return Variant::with(relations: $withRelations ?? [])->get();
    }

    public function find(string $uuid): ?Variant
    {
        return Variant::find($uuid) ?? null;
    }

    public function findByName(string $name): ?Variant
    {
        return Variant::where('name', $name)->first();
    }

    public function fill(Variant $variant, string $name, string $description, ?Variant $parentVariant): Variant
    {
        $variant->name = $name;
        $variant->description = $description;

        if ($parentVariant) {
            $variant->parentVariant()->associate($parentVariant);
        }

        return $variant;
    }

    public function save(Variant $variant): Variant
    {
        $variant->save();
        return $variant;
    }

    public function delete(Variant $variant): void
    {
        $variant->delete();
    }
}

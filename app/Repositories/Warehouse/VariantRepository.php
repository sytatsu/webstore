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

    public function fill(Variant $variant, string $name, string $description): Variant
    {
        $variant->name = $name;
        $variant->description = $description;

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

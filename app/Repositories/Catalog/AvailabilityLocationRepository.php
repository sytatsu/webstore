<?php

namespace App\Repositories\Catalog;

use App\Models\Availability;
use App\Models\AvailabilityLocation;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityLocationRepository
{
    public function all(): Collection
    {
        return AvailabilityLocation::all();
    }

    public function find(string $uuid): ?AvailabilityLocation
    {
        return AvailabilityLocation::find($uuid);
    }

    public function findByLabel(string $label): ?AvailabilityLocation
    {
        return AvailabilityLocation::where('label', $label)->first();
    }

    public function fill(AvailabilityLocation $availabilityLocation, array $data): AvailabilityLocation
    {
        $availabilityLocation->label = $data['label'];

        return $availabilityLocation;
    }

    public function save(AvailabilityLocation $availabilityLocation): AvailabilityLocation
    {
        $availabilityLocation->save();
        return $availabilityLocation;
    }
}

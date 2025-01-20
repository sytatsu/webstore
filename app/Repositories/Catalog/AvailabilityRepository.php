<?php

namespace App\Repositories\Catalog;

use App\Models\Availability;
use App\Models\AvailabilityLocation;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityRepository
{
    public function all(?array $withRelations): Collection
    {
        return Availability::with($withRelations ?? [])->get();
    }

    public function find(string $uuid): ?Availability
    {
        return Availability::find($uuid);
    }

    public function fill(Availability $availability, AvailabilityLocation $availabilityLocation, array $data): Availability
    {
        $availability->availabilityType = $data['availability_type'];
        $availability->availabilityQuantity = $data['availability_quantity'];

        $availability->availabilityLocation()->associate($availabilityLocation);

        return $availability;
    }

    public function save(Availability $availability): Availability
    {
        $availability->save();
        return $availability;
    }
}

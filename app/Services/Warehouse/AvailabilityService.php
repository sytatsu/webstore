<?php

namespace App\Services\Warehouse;

use App\Enums\ProductVariantType;
use App\Models\Availability;
use App\Models\AvailabilityLocation;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use App\Repositories\Warehouse\AvailabilityLocationRepository;
use App\Repositories\Warehouse\AvailabilityRepository;
use App\Repositories\Warehouse\ProductRepository;
use App\Repositories\Warehouse\ProductVariantRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AvailabilityService
{

    public function __construct(
        private readonly AvailabilityRepository $availabilityRepository,
        private readonly AvailabilityLocationRepository $availabilityLocationRepository,
    ) {
        //
    }

    public function getLocationList(): SupportCollection
    {
        return $this->availabilityLocationRepository->all()->pluck('label', 'label');
    }

    public function newAvailability(): Availability
    {
        return new Availability();
    }

    public function newAvailabilityLocation(): AvailabilityLocation
    {
        return new AvailabilityLocation();
    }

    public function availabilityLocationFirstByLabelOrCreate(string $label): AvailabilityLocation
    {
        return $this->availabilityLocationRepository->findByLabel(label: $label)
            ?? $this->storeAvailabilityLocation(availabilityLocation: null, data: ['label' => $label]);
    }

    public function storeAvailability(?Availability $availability, ?AvailabilityLocation $availabilityLocation, array $data): Availability
    {
        if ( ! isset($availabilityLocation)) {
            $availabilityLocation = $this->availabilityLocationFirstByLabelOrCreate(label: $data['location']['label'] ?? 'N/A');
        }

        if ( ! isset($availability)) {
            $availability = $this->newAvailability();
        }

        $this->availabilityRepository->fill(
            availability: $availability,
            availabilityLocation: $availabilityLocation,
            data: $data,
        );

        $this->availabilityRepository->save($availability);

        return $availability;
    }

    public function storeAvailabilityLocation(?AvailabilityLocation $availabilityLocation, array $data): AvailabilityLocation {
        if ( ! isset($availabilityLocation)) {
            $availabilityLocation = $this->newAvailabilityLocation();
        }

        $this->availabilityLocationRepository->fill(
            availabilityLocation: $availabilityLocation,
            data: $data
        );

        $this->availabilityLocationRepository->save($availabilityLocation);

        return $availabilityLocation;
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Collection;
use Lunar\Models\Country;

class CountryRepository
{
    /**
     * @return Collection<Country>
     */
    public function getAvailableCountries(): Collection
    {
        return Country::whereIn('iso3', ['NLD'])->get();
    }
}

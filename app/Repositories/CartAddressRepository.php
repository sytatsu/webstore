<?php

declare(strict_types=1);

namespace App\Repositories;

use Lunar\Models\CartAddress;

class CartAddressRepository
{
    public function find(int $id): ?CartAddress
    {
        return CartAddress::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $address = $this->find($id);
        if (!$address) {
            return false;
        }

        return $address->update($data);
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories;

use Lunar\Models\Cart;
use Lunar\Models\Order;

class OrderRepository
{
    public function find(int|string $id): ?Order
    {
        return Order::find($id);
    }

    public function findFirstByCart(Cart $cart): ?Order
    {
        return $cart->orders()->first();
    }
}

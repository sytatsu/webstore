<?php


namespace App\Observers;

use App\Mail\Sytatsu\Orders;
use Illuminate\Support\Facades\Mail;
use Lunar\Models\Contracts\Order as OrderContract;
use Lunar\Models\Order as OrderModel;
use Lunar\Observers\OrderObserver as LunarOrderObserver;

class OrderObserver extends LunarOrderObserver
{
    public function updating(OrderModel|OrderContract $order): void
    {
        parent::updating($order);

        if ($order->getOriginal('status') != $order->status) {
            if ($order->status === 'payment-received') {
                Mail::send(new Orders\Confirmation(order: $order));
            }

//            if ($order->status === 'dispatched') {
//                Mail::send(new Orders\Dispatched(order: $order));
//            }
        }
    }
}

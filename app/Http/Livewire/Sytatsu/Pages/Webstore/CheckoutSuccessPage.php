<?php


namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use Lunar\Models\Order;

class CheckoutSuccessPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.order-success';
    protected ?string $title = 'Order Successful';

    public string $order_id;
    public Order $order;

    protected array $queryString = ['order_id'];

    public function mount()
    {
        $this->order = Order::find($this->order_id);
    }
}

<?php


namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\CheckoutService;
use Lunar\Models\Order;

class CheckoutSuccessPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.order-success';
    protected ?string $title = 'Order Successful';

    public string $order_id;
    public Order $order;

    protected array $queryString = ['order_id'];

    private CheckoutService $checkoutService;

    public function boot(CheckoutService $checkoutService): void
    {
        $this->checkoutService = $checkoutService;
    }

    public function mount(): void
    {
        $this->order = $this->checkoutService->findOrder($this->order_id);
    }
}

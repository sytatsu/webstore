<?php


namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Services\CartService;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;

class CheckoutPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.checkout';
    protected ?string $title = 'Checkout';

    private CartService $cartService;
    public array $lines = [];

    protected string $paymentType = 'stripe';
    public ?string $payment_intent = null;
    public ?string $payment_intent_client_secret = null;

    protected $queryString = [
        'payment_intent',
        'payment_intent_client_secret',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        if ($this->payment_intent) {
            $payment = Payments::driver($this->paymentType)->cart($this->cart)->withData([
                'payment_intent_client_secret' => $this->payment_intent_client_secret,
                'payment_intent' => $this->payment_intent,
            ])->authorize();

            if ($payment->success) {
                return redirect()->route('sytatsu.webstore.checkout.success', ['order_id' => $this->cart->orders()->first()->id]);
            }
        }

        $this->mapLines();
    }

    public function getCartProperty()
    {
        return $this->cartService->getCurrentCart();
    }

    public function getLinesProperty(): array
    {
        $this->mapLines();
        return $this->lines;
    }

    public function mapLines(): void
    {
        $this->lines = $this->cartService->mapCartLines();
    }
}

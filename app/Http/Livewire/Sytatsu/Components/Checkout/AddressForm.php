<?php

namespace App\Http\Livewire\Sytatsu\Components\Checkout;

use App\Enums\AddressTypeEnum;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Models\Cart;

/**
 * @property Cart $cart
 */
class AddressForm extends Component
{
    public string $addressType;
    public  array $address = [];

    private CartService $cartService;
    private CheckoutService $checkoutService;

    protected $listeners = [
        'save-address' => 'saveAddress'
    ];

    public function boot(
        CartService $cartService,
        CheckoutService $checkoutService
    ): void {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    public function mount(string $addressType): void
    {
        $this->addressType = $addressType;
    }

    public function getCartProperty()
    {
        return $this->cartService->getCurrentCart();
    }

    public function getCountriesProperty(): Collection
    {
        return $this->checkoutService->getAvailableCountries();
    }

    protected function rules(): array
    {
        return $this->checkoutService->getAddressValidationRules();
    }

    public function saveAddress(): void
    {
        try {
            $this->validate($this->rules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('address-save-failed');
            throw $e;
        }

        if (isset($this->address['id'])) {
            $this->checkoutService->updateCartAddress($this->address['id'], $this->cleanAddressArray($this->address));
        } else {
            if ($this->addressType === AddressTypeEnum::SHIPPING->value) {
                $this->address = $this->checkoutService->setShippingAddress($this->cart, $this->address);
            }

            if ($this->addressType === AddressTypeEnum::BILLING->value) {
                $this->address = $this->checkoutService->setBillingAddress($this->cart, $this->address);
            }
        }

        $this->dispatch('address-updated');
    }

    private function cleanAddressArray(array $address): array
    {
        unset($address['id']);
        unset($address['created_at']);
        unset($address['updated_at']);
        unset($address['deleted_at']);

        return $address;
    }

    public function render(): View
    {
        return view('sytatsu.components.livewire.checkout.address-form');
    }
}

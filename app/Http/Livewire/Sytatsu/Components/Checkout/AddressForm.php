<?php

namespace App\Http\Livewire\Sytatsu\Components\Checkout;

use App\Enums\AddressTypeEnum;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Lunar\Models\Cart;

/**
 * @property Cart $cart
 */
class AddressForm extends Component
{
    public string $addressType;
    public array $address = [];

    private CartService $cartService;
    private CheckoutService $checkoutService;

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

    protected function validationAttributes(): array
    {
        return [
            'address.first_name'    => 'first name',
            'address.last_name'     => 'last name',
            'address.line_one'      => 'street',
            'address.line_two'      => 'house number',
            'address.postcode'      => 'postal code',
            'address.city'          => 'city',
            'address.country_id'    => 'country',
            'address.contact_email' => 'email',
            'address.contact_phone' => 'phone number',
        ];
    }

    #[On('save-address')]
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
            $this->address = $this->cleanAddressArray($this->address);
        } elseif ($this->addressType === AddressTypeEnum::SHIPPING->value) {
            $this->address = $this->checkoutService->setShippingAddress($this->cart, $this->address);
        } elseif ($this->addressType === AddressTypeEnum::BILLING->value) {
            $this->address = $this->checkoutService->setBillingAddress($this->cart, $this->address);
        } else {
            $this->dispatch('address-save-failed');
            return;
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

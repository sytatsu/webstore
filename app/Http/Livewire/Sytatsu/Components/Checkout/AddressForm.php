<?php

namespace App\Http\Livewire\Sytatsu\Components\Checkout;

use App\Enums\AddressTypeEnum;
use App\Services\CartService;
use Livewire\Component;
use Lunar\Models\Cart;

/**
 * @property Cart $cart
 */
class AddressForm extends Component
{
    public string $addressType;
    private array $address = [];

    private CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(AddressTypeEnum $addressType): void
    {
        $this->addressType = $addressType->value;
    }

    public function getCartProperty()
    {
        return $this->cartService->getCurrentCart();
    }

    protected function rules(): array
    {
        return [
            'address.first_name'    => 'required',
            'address.last_name'     => 'required',
            'address.line_one'      => 'required',
            'address.line_two'      => 'required',
            'address.line_three'    => 'nullable',
            'address.postcode'      => 'required',
            'address.city'          => 'required',
            'address.country_id'    => 'required',
            'address.contact_email' => 'required|email',
            'address.contact_phone' => 'nullable',
        ];
    }

    public function saveAddress(): void
    {
        $this->validate($this->rules());

        if ($this->addressType === AddressTypeEnum::SHIPPING->value) {
            $this->cart->setShippingAddress($this->address);
        }

        if ($this->addressType === AddressTypeEnum::BILLING->value) {
            $this->cart->setBillingAddress($this->address);
        }

        $this->dispatch('address-updated');
    }

    public function render()
    {
        return view('sytatsu.components.livewire.checkout.address-form');
    }
}

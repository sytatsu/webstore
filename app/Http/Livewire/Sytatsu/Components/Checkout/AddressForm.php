<?php

namespace App\Http\Livewire\Sytatsu\Components\Checkout;

use App\Enums\AddressTypeEnum;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;

/**
 * @property Cart $cart
 */
class AddressForm extends Component
{
    public string $addressType;
    public  array $address = [];

    private CartService $cartService;

    protected $listeners = [
        'save-address' => 'saveAddress'
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
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
        return Country::whereIn('iso3', ['NLD'])->get();
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

        if (isset($this->address['id'])) {
            CartAddress::find($this->address['id'])->update($this->cleanAddressArray($this->address));
        } else {
            if ($this->addressType === AddressTypeEnum::SHIPPING->value) {
                $this->cart->setShippingAddress($this->address);
                $this->cart->save();
                $this->address = $this->cart->refresh()->shippingAddress->toArray();

            }

            if ($this->addressType === AddressTypeEnum::BILLING->value) {
                $this->cart->setBillingAddress($this->address);
                $this->cart->save();
                $this->address = $this->cart->refresh()->billingAddress->toArray();
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

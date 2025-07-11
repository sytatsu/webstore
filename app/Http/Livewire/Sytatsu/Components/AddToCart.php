<?php

namespace App\Http\Livewire\Sytatsu\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Base\Purchasable;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart as LunarCart;

class AddToCart extends Component
{
    /**
     * The purchasable model we want to add to the cart.
     *
     * @var ?Purchasable
     */
    public ?Purchasable $purchasable = null;

    /**
     * The quantity to add to cart.
     *
     * @var int
     */
    public int $quantity = 1;

    public function rules(): array
    {
        return [
            'quantity' => 'required|numeric|min:1|max:10000',
        ];
    }

    public function getCartProperty(): LunarCart
    {
        return CartSession::current();
    }

    public function getAvailableStockProperty(): int
    {
        $inCart = $this->cart->lines->first(fn($line) => $line->purchasable_id === $this->purchasable->id)?->quantity;
        $avilableStock = $this->purchasable->stock;
        return $avilableStock - $inCart;
    }

    public function updatedQuantity($quantity): int
    {
        if (!is_int($quantity)) {
            return $this->quantity = 1;
        }

        if ($quantity >= $this->availableStock) {
            return $this->quantity = $this->availableStock;
        }

        if ($quantity <= 1) {
            return $this->quantity = 1;
        }

        return $quantity;
    }

    public function increment(): void
    {
        $this->quantity++;
        $this->updatedQuantity($this->quantity);
    }

    public function decrement(): void
    {
        $this->quantity--;
        $this->updatedQuantity($this->quantity);
    }

    public function addToCart()
    {
        $this->validate();

        if ($this->purchasable->purchasable !== 'always' && $this->purchasable->stock < $this->quantity) {
            $this->addError('quantity', 'The quantity exceeds the available stock.');
            return;
        }

        CartSession::manager()->add($this->purchasable, $this->quantity);
        $this->dispatch('add-to-cart');
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.add-to-cart');
    }
}

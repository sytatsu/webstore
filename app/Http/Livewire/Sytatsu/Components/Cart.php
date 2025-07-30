<?php

namespace App\Http\Livewire\Sytatsu\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart as LunarCart;
use Lunar\Models\CartLine;

class Cart extends Component
{
    public array $lines = [];
    public bool $cartOpen = false;

    protected $listeners = [
        'add-to-cart' => 'handleAddToCart',
    ];

    public function mount(): void
    {
        $this->mapLines();
    }

    public function getCartProperty(): LunarCart
    {
        return CartSession::current();
    }

    public function getLinesProperty(): array
    {
        $this->mapLines();
        return $this->lines;
    }

    public function getCartTotalQuantityProperty(): int
    {
        return array_sum(collect($this->lines)->map(fn (array $mappedCartLine) => $mappedCartLine['quantity'])->toArray()) ?? 0;
    }

    public function rules(): array
    {
        return [
            'lines.*.quantity' => 'required|numeric|min:1|max:10000',
        ];
    }

    public function incrementLine(string $index)
    {
        $this->lines[$index]['quantity']++;

        if ($this->lines[$index]['quantity'] >  $this->lines[$index]['purchasable']->stock) {
             $this->lines[$index]['quantity'] = $this->lines[$index]['purchasable']->stock;
        }

        $this->updateLines();
    }

    public function decrementLine(string $index)
    {
        $this->lines[$index]['quantity']--;

        if ($this->lines[$index]['quantity'] <= 0) {
            $this->removeLine($this->lines[$index]['id']);
        }

        $this->updateLines();
    }

    public function updateLines(): void
    {
        $this->validate();

        CartSession::updateLines(
            collect($this->lines)
        );
        $this->mapLines();
        $this->dispatch('cart-updated');
    }

    public function removeLine($id)
    {
        CartSession::remove($id);
        $this->mapLines();
    }

    public function handleAddToCart(): void
    {
        $this->mapLines();
        $this->cartOpen = true;
    }

    public function mapLines(): void
    {
        $this->lines = $this->cart->lines->map(fn (CartLine $line) => [
             'id' => $line->id,
             'purchasable' => $line->purchasable,
             'product' => $line->purchasable->product,
             'option_id' => $line->purchasable->option_id,
             'identifier' => $line->purchasable->getIdentifier(),
             'quantity' => $line->quantity,
             'description' => $line->purchasable->getDescription(),
             'thumbnail' => $line->purchasable->getThumbnail()?->getUrl(),
             'option' => $line->purchasable->getOption(),
             'options' => $line->purchasable->getOptions()->implode(' / '),
             'sub_total' => $line->subTotal->formatted(),
             'unit_price' => $line->unitPrice->formatted(),
        ])->toArray();
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.cart');
    }
}

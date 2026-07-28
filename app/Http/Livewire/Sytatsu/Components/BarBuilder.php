<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Models\BarBuilderBaseColor;
use App\Models\BarBuilderCapCombo;
use App\Models\BarBuilderIcon;
use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Models\Product;

class BarBuilder extends Component
{
    private CartService $cartService;

    public Product $product;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function getCatalogProperty(): array
    {
        return [
            'minCaps' => $this->capsCatalog()->min('caps'),
            'maxCaps' => $this->capsCatalog()->max('caps'),
            'caps' => $this->capsCatalog()->values()->all(),
            'baseColors' => BarBuilderBaseColor::query()->ordered()->get()
                ->map(fn (BarBuilderBaseColor $color) => [
                    'id' => $color->id,
                    'name' => $color->name,
                    'hex' => $color->hex,
                    'available' => $color->enabled,
                ])->all(),
            'capCombos' => BarBuilderCapCombo::query()->ordered()->get()
                ->map(fn (BarBuilderCapCombo $combo) => [
                    'id' => $combo->id,
                    'name' => $combo->name,
                    'cap' => $combo->cap_hex,
                    'text' => $combo->text_hex,
                    'available' => $combo->enabled,
                ])->all(),
            'icons' => BarBuilderIcon::query()->ordered()->enabled()->get()
                ->map(fn (BarBuilderIcon $icon) => [
                    'id' => $icon->id,
                    'name' => $icon->name,
                    'cx' => $icon->cx,
                    'cy' => $icon->cy,
                    'scale' => $icon->scale,
                    'path' => $icon->path,
                ])->all(),
        ];
    }

    /**
     * One entry per selectable cap count, backed by a real product variant + price.
     */
    private function capsCatalog(): \Illuminate\Support\Collection
    {
        return $this->product->variants
            ->map(function ($variant) {
                $caps = $variant->values->first()?->meta['caps'] ?? null;

                if (!$caps) {
                    return null;
                }

                $price = $variant->pricing()->get()->matched->price;

                return [
                    'caps' => (int) $caps,
                    'variant_id' => $variant->id,
                    'price' => $price->formatted(),
                    'price_minor' => $price->value,
                ];
            })
            ->filter()
            ->sortBy('caps');
    }

    public function addToCart(array $payload): void
    {
        $meta = $payload['meta'] ?? [];
        $caps = $meta['caps'] ?? [];
        $capsCatalog = $this->capsCatalog();

        $capsEntry = $capsCatalog->firstWhere('caps', count($caps));

        if (!$capsEntry) {
            $this->dispatch('bar-builder-error', message: __('Please choose between :min and :max caps.', [
                'min' => $capsCatalog->min('caps'),
                'max' => $capsCatalog->max('caps'),
            ]));

            return;
        }

        $baseColor = BarBuilderBaseColor::query()->enabled()
            ->where('hex', $meta['base_colour']['hex'] ?? null)
            ->first();

        if (!$baseColor) {
            $this->dispatch('bar-builder-error', message: __('That base colour is not available.'));

            return;
        }

        $enabledCombos = BarBuilderCapCombo::query()->enabled()->get();
        $enabledIconIds = BarBuilderIcon::query()->enabled()->pluck('id');

        foreach ($caps as $cap) {
            $comboMatch = $enabledCombos->first(fn (BarBuilderCapCombo $combo) => $combo->cap_hex === ($cap['colour']['hex'] ?? null)
                && $combo->text_hex === ($cap['text_colour']['hex'] ?? null));

            if (!$comboMatch) {
                $this->dispatch('bar-builder-error', message: __('One of the cap colour combinations is not available.'));

                return;
            }

            if (!empty($cap['icon']) && !$enabledIconIds->contains($cap['icon']['id'] ?? null)) {
                $this->dispatch('bar-builder-error', message: __('One of the selected icons is not available.'));

                return;
            }
        }

        $variant = $this->product->variants->firstWhere('id', $capsEntry['variant_id']);

        $this->cartService->addLine($variant, 1, [
            'bar_builder' => [
                'text' => $meta['text'] ?? '',
                'reference' => $meta['reference'] ?? '',
                'base_colour' => $meta['base_colour'] ?? null,
                'caps' => $caps,
            ],
        ]);

        $this->dispatch('cart-updated');
        $this->dispatch('add-to-cart');
        $this->dispatch('bar-builder-added', reference: $meta['reference'] ?? '');
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.bar-builder', [
            'catalog' => $this->catalog,
        ]);
    }
}

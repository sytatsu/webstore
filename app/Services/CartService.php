<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\TaxRepository;
use Illuminate\Support\Collection;
use Lunar\Base\Purchasable;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\CartSession;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart as LunarCart;
use Lunar\Models\CartLine;
use Lunar\Models\TaxClass;

readonly class CartService
{
    public function __construct(
        private TaxRepository $taxRepository
    ) {
    }

    public function updateLines(array $lines): array
    {
        $normalizedLines = $this->normalizeQuantities($lines);
        $validLines = $this->removeInvalidLines($normalizedLines);

        $this->syncWithCartSession($validLines);

        return $this->mapCartLines();
    }

    public function addLine(Purchasable $purchasable, int $quantity): void
    {
        CartSession::manager()->add($purchasable, $quantity);
    }

    public function incrementLine(array $lines, string $index): array
    {
        if (!isset($lines[$index])) {
            return $lines;
        }

        $lines[$index]['quantity']++;

        if ($lines[$index]['purchasable']->purchasable === 'in_stock' && $lines[$index]['quantity'] >  $lines[$index]['purchasable']->stock) {
            $lines[$index]['quantity'] = $lines[$index]['purchasable']->stock;
        }

        return $this->updateLines($lines);
    }

    public function decrementLine(array $lines, string $index): array
    {
        if (!isset($lines[$index])) {
            return $lines;
        }

        $lines[$index]['quantity']--;

        return $this->updateLines($lines);
    }

    public function updateQuantity(array $lines, string $index, int $quantity): array
    {
        if (!isset($lines[$index])) {
            return $lines;
        }

        $lines[$index]['quantity'] = max(0, $quantity);

        return $this->updateLines($lines);
    }

    public function removeLine(int $lineId): void
    {
        CartSession::remove($lineId);
    }

    public function mapCartLines(): array
    {
        $cart = $this->getCurrentCart()->refresh()->calculate();

        if ($cart->shippingAddress) {
            $cart->setShippingOption($this->recalculateShippingOption($cart));
            $cart->calculate();
        }

        return $cart->lines
            ->filter(fn (CartLine $line) => $line->purchasable_type !== \Lunar\DataTypes\ShippingOption::class)
            ->map(function (CartLine $line) {
                // If the product is not published (or missing due to scope), it might be null.
                // We should handle this gracefully to avoid "translateAttribute() on null"
                $product = $line->purchasable->product;
                $description = $product ? $line->purchasable->getDescription() : ($line->purchasable->sku ?? 'Unknown Product');

                return [
                    'id' => $line->id,
                    'purchasable' => $line->purchasable,
                    'product' => $product,
                    'identifier' => $line->purchasable->getIdentifier(),
                    'quantity' => $line->quantity,
                    'description' => $description,
                    'thumbnail' => $line->purchasable->getThumbnail()?->getUrl(),
                    'option' => $line->purchasable->getOption(),
                    'options' => $line->purchasable->getOptions()->map(fn (string $option) => __($option))->implode(' / '),
                    'sub_total' => $line->subTotal->formatted(),
                    'unit_price' => $line->unitPrice->formatted(),
                ];
            })->toArray();
    }

    public function getTotalQuantity(): int
    {
        return array_sum(
            $this->getCurrentCart()->lines
                ->filter(fn (CartLine $line) => $line->purchasable_type !== \Lunar\DataTypes\ShippingOption::class)
                ->map(fn (CartLine $line) => $line->quantity)
                ->toArray()
        );
    }

    public function getAvailableStockProperty(Purchasable $purchasable): int
    {
        $inCart = $this->getCurrentCart()->lines->first(fn($line) => $line->purchasable_id === $purchasable->id)?->quantity;
        $availableStock = $purchasable->stock;
        return $availableStock - $inCart;
    }

    public function getCurrentCart(): LunarCart
    {
        return CartSession::current();
    }

    public function forgetCurrentCart(): void
    {
        CartSession::forget();
    }

    public function isCartDisabled(): bool
    {
        // 1. Check for explicit disable flag (merged by middleware or manually)
        if (request()->has('disable_cart')) {
            return true;
        }

        // 2. Direct route check (works for initial load of checkout pages)
        if (request()->routeIs('sytatsu.webstore.checkout*')) {
            return true;
        }

        // 3. Referer check for Livewire requests
        $referer = request()->header('Referer');
        if ($referer) {
            $path = parse_url($referer, PHP_URL_PATH) ?? '';
            // Robust check: match /checkout or /checkout/success (handling potential locale prefixes like /en/checkout)
            // Matches any path that contains "/checkout" as a full segment
            if (preg_match('/\/checkout(\/|$)/', $path)) {
                return true;
            }
        }

        return false;
    }

    public function getAvailableShippingOptions(LunarCart $cart): Collection
    {
        return ShippingManifest::getOptions($cart)->filter(function (ShippingOption $shippingOption) use ($cart) {
            $isFreeOption = str_ends_with($shippingOption->identifier, 'FREETARDEL');
            if ($this->canHaveFreeShipping($cart)) {
                return $isFreeOption;
            }

            return !$isFreeOption;
        })->unique('identifier');
    }

    public function getShippingOption(): ?ShippingOption
    {
        return $this->getCurrentCart()->shippingAddress
            ? $this->getCurrentCart()->getShippingOption()
            : null;
    }

    public function recalculateShippingOption(LunarCart $cart): ShippingOption
    {
        $shippingOptions = $this->getAvailableShippingOptions($cart);
        $currentOption = $cart->getShippingOption();

        if ($currentOption) {
            $matchingOption = $shippingOptions->first(fn($option) => $option->getIdentifier() === $currentOption->getIdentifier());
            if ($matchingOption) {
                return $matchingOption;
            }
        }

        return $shippingOptions->first();
    }

    public function getDefaultShippingOption(LunarCart $cart): ShippingOption
    {
        return $this->getAvailableShippingOptions($cart)->first();
    }

    public function getDefaultShippingTaxClass(): TaxClass
    {
        return $this->taxRepository->getDefaultShippingTaxClass();
    }

    private function canHaveFreeShipping (LunarCart $cart): bool
    {
        return $cart->subTotalDiscounted->value > config('lunar.shipping.free_delivery_threshold', 7000);
    }

    private function normalizeQuantities(array $lines): array
    {
        return array_map(function (array $line) {
            $line['quantity'] = ($line['purchasable']->purchasable === 'in_stock' && $line['quantity'] > $line['purchasable']->stock)
                ? $line['purchasable']->stock
                : $line['quantity'];

            return $line;
        }, $lines);
    }

    private function removeInvalidLines(array $lines): array
    {
        return array_filter($lines, function (array $line) {
            if ($line['quantity'] <= 0) {
                CartSession::remove($line['id']);
                return false;
            }
            return true;
        });
    }

    private function syncWithCartSession(array $lines): void
    {
        CartSession::updateLines(collect($lines));
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Lunar\Base\Purchasable;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart as LunarCart;
use Lunar\Models\CartLine;

class CartService
{
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
        return $this->getCurrentCart()->lines->map(fn (CartLine $line) => [
            'id' => $line->id,
            'purchasable' => $line->purchasable,
            'product' => $line->purchasable->product,
            'identifier' => $line->purchasable->getIdentifier(),
            'quantity' => $line->quantity,
            'description' => $line->purchasable->getDescription(),
            'thumbnail' => $line->purchasable->getThumbnail()?->getUrl(),
            'option' => $line->purchasable->getOption(),
            'options' => $line->purchasable->getOptions()->map(fn (string $option) => __($option))->implode(' / '),
            'sub_total' => $line->subTotal->formatted(),
            'unit_price' => $line->unitPrice->formatted(),
        ])->toArray();
    }

    public function getTotalQuantity(array $lines): int
    {
        return array_sum(array_column($lines, 'quantity'));
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

    public static function make(): static
    {
        return new static();
    }
}

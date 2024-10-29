<?php

namespace App\DataObject;

class Currency
{
    public function __construct(
        private readonly int $price
    ) {
        //
    }

    static public function from(int|float $price): ?self
    {
        return match (true) {
            is_float($price) => new self($price * 100),
            is_int($price) => new self($price),
            default => null,
        };
    }

    public function formatted(): string
    {
        return '€' . $this->string();
    }

    public function string(): string
    {
        return number_format($this->price / 100, 2, ',');
    }

    public function integer(): int
    {
        return $this->price;
    }
}

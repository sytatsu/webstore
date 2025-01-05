<?php

namespace App\DataObject;

class Currency
{
    public function __construct(
        private readonly int $price
    ) {
        //
    }

    static public function from(int|float|string $price): ?self
    {
        $match = match (true) {
            is_float($price) => new self($price * 100),
            is_int($price) => new self($price),
            is_string($price) => new self(floatval(str_replace(',', '.', $price)) * 100),
            default => null,
        };

//        dd($price, $match);

        return $match;
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

    public function __toString(): string
    {
        return $this->string();
    }

    public function __toInteger(): int
    {
        return $this->integer();
    }
}

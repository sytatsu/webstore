<?php

namespace App\Casts;

use App\DataObject\Currency;
use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CurrencyCast implements CastsAttributes
{

    /**
     * @throws \Exception
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Currency
    {
        if (!is_integer($value)) {
            throw new Exception('\$value should be integer, ' . gettype($value) . ' given.');
        }

        return Currency::from($value);
    }

    /**
     * @throws \Exception
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        if (!$value instanceof Currency) {
            throw new Exception('$value should be instance of currency, ' . gettype($value) . 'given.');
        }

        return $value->integer();
    }
}

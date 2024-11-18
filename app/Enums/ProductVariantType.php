<?php

namespace App\Enums;

use Nette\Utils\Arrays;

enum ProductVariantType: string
{
    case GENERIC = 'GENERIC';
    case UNIQUE = 'UNIQUE';

    public function translation(): string
    {
        return match ($this) {
            self::UNIQUE => __('Unique'),
            self::GENERIC => __('Generic'),
        };
    }

    public static function list(): array
    {
        return Arrays::mapWithKeys(self::cases(), function ($value) {
            return [$value->name, $value->translation()];
        });
    }
}

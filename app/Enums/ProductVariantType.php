<?php

namespace App\Enums;

enum ProductVariantType
{
    case UNIQUE;
    case GENERIC;

    public function translations(): array
    {
        return match ($this) {
            self::UNIQUE => __('Unique'),
            self::GENERIC => __('Generic'),
        };
    }
}

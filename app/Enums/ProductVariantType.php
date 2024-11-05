<?php

namespace App\Enums;

enum ProductVariantType
{
    case UNIQUE;
    case GENERIC;

    public function translation(): string
    {
        return match ($this) {
            self::UNIQUE => __('Unique'),
            self::GENERIC => __('Generic'),
        };
    }
}

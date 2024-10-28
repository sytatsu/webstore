<?php

namespace App\Enums;

enum ProductTypeEnum
{
    case DIGITAL;
    case PRINTED;
    case THIRD_PARTY;
    case CUSTOM_REQUEST;

    public function translation(): string
    {
        return match ($this) {
            self::DIGITAL => __('Digital'),
            self::PRINTED => __('Printed'),
            self::THIRD_PARTY => __('3rd-Party'),
            self::CUSTOM_REQUEST => __('Custom Request'),
        };
    }
}

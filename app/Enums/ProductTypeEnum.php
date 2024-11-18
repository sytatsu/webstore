<?php

namespace App\Enums;

use Nette\Utils\Arrays;

enum ProductTypeEnum: string
{
    case PRINTED = 'PRINTED';
    case DIGITAL = 'DIGITAL';
    case THIRD_PARTY = 'THIRD_PARTY';

    public function translation(): string
    {
        return match ($this) {
            self::PRINTED => __('Printed'),
            self::DIGITAL => __('Digital'),
            self::THIRD_PARTY => __('3rd-Party'),
        };
    }

    public static function list(): array
    {
        return Arrays::mapWithKeys(self::cases(), function ($value) {
            return [$value->name, $value->translation()];
        });
    }
}

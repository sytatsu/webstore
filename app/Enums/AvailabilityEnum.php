<?php

namespace App\Enums;

use Nette\Utils\Arrays;

enum AvailabilityEnum: string
{
    case STOCK = 'STOCK';
    case DOWNLOAD = 'DOWNLOAD';
    case ON_REQUEST = 'ON_REQUEST';

    public function translation(): string
    {
        return match ($this) {
            self::STOCK => __('Stock'),
            self::DOWNLOAD => __('Download'),
            self::ON_REQUEST => __('On Request'),
        };
    }

    public static function list(): array
    {
        return Arrays::mapWithKeys(self::cases(), function ($value) {
            return [$value->name, $value->translation()];
        });
    }
}

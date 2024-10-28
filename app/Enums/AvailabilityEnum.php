<?php

namespace App\Enums;

enum AvailabilityEnum
{
    case STOCK;
    case DOWNLOAD;
    case ON_REQUEST;

    public function translation(): string
    {
        return match ($this) {
            self::STOCK => __('Stock'),
            self::DOWNLOAD => __('Download'),
            self::ON_REQUEST => __('On Request'),
        };
    }
}

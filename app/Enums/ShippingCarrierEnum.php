<?php

namespace App\Enums;

enum ShippingCarrierEnum: string
{
    case DHL = 'dhl';
    case POSTNL = 'postnl';

    public function label(): string
    {
        return match ($this) {
            self::DHL => 'DHL',
            self::POSTNL => 'PostNL',
        };
    }

    public function trackingUrl(string $trackingNumber): string
    {
        return match ($this) {
            self::DHL => "https://www.dhl.com/nl-en/home/tracking.html?tracking-id={$trackingNumber}",
            self::POSTNL => "https://jouw.postnl.nl/track-and-trace/{$trackingNumber}",
        };
    }
}

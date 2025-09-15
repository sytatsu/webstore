<?php

namespace App\Enums;

enum AddressTypeEnum: string
{
    case SHIPPING = 'shipping';
    case BILLING = 'billing';
}

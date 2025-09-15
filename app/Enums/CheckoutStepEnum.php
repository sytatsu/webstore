<?php

namespace App\Enums;

enum CheckoutStepEnum: string
{
    case ADDRESS = 'address';
    case SHIPPING_OPTION = 'shipping_option';
    case PAYMENT = 'payment';
}

<?php

namespace App\Enums;

enum AvailabilityEnum: string
{
    case STOCK = 'Stock';
    case DOWNLOAD = 'Download';
    case ON_REQUEST = 'Request';
}

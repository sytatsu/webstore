<?php

namespace App\Enums;

enum ProductTypeEnum: string
{
    case DIGITAL = 'Digital';
    case PRINTED = '3D Printed';
    case THIRD_PARTY = 'Third Party';
    case CUSTOM_REQUEST = 'Custom Request';
}

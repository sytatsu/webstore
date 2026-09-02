<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ShippingOptions extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Shipping Options';

    protected static ?string $navigationGroup = 'Settings';
}

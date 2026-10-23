<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ShippingOptions extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Shipping Options';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
}

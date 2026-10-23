<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class BarBuilder extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Bar Builder';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
}

<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class BarBuilder extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Bar Builder';

    protected static ?string $navigationGroup = 'Settings';
}

<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Bundles extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Bundles';

    protected static ?string $navigationGroup = 'Settings';
}

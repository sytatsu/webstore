<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class WebstoreSettings extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string | \UnitEnum | null $navigationGroup = "Settings";
}

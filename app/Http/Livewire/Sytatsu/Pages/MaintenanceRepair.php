<?php

namespace App\Http\Livewire\Sytatsu\Pages;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;

class MaintenanceRepair extends SytatsuBasePage
{
    protected string $view = 'sytatsu.maintenance-repair';
    protected ?string $title = 'Maintenance & Repair';
    protected ?string $description = 'Is your 3D printer not performing as it should? We offer maintenance and repair services to get you back to printing in no time.';
}

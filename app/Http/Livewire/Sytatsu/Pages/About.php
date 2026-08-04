<?php

namespace App\Http\Livewire\Sytatsu\Pages;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;

class About extends SytatsuBasePage
{
    protected string $view = 'sytatsu.about';
    protected ?string $title = 'About us';
    protected ?string $description = 'We are Angela and Steve, the team behind Sytatsu. What started as a shared hobby has grown into a real 3D printing business.';
}

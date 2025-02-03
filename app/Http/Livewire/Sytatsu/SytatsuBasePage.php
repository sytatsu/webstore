<?php

namespace App\Http\Livewire\Sytatsu;

use App\Http\Livewire\BasePage;

class SytatsuBasePage extends BasePage
{
    protected ?string $title = null;
    protected string $appName = 'Sytatsu';
    protected string $view;
    protected string $layout = 'layouts.sytatsu-layout';

    protected array $viewAttributes = [];
    protected array $layoutAttributes = [];
}

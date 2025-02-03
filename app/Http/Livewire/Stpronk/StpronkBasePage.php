<?php

namespace App\Http\Livewire\Stpronk;

use App\Http\Livewire\BasePage;

class StpronkBasePage extends BasePage
{
    protected ?string $title = 'Web-Magician';
    protected string $appName = 'StPronk';
    protected string $view;
    protected string $layout = 'layouts.stpronk-layout';

    protected array $viewAttributes = [];
    protected array $layoutAttributes = [];
}



<?php

namespace App\Http\Livewire\Sytatsu\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class SocialCollection extends Component
{
    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.social-collection');
    }
}

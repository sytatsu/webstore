<?php

namespace App\Http\Livewire\Stpronk\Pages;

use Livewire\Component;
use function view;

class Welcome extends Component
{
    public function render()
    {
        return view('stpronk.welcome')
            ->layout('layouts.stpronk-layout');
    }
}

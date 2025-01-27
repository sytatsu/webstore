<?php

namespace App\Http\Livewire\Sytatsu\Pages;

use Livewire\Component;
use function view;

class Welcome extends Component
{
    public function render()
    {
        return view('sytatsu.welcome')
            ->layout('layouts.sytatsu-layout', [
                'center' => true,
            ]);
    }
}

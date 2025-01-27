<?php

namespace App\Http\Livewire\Sytatsu\Pages;

use Livewire\Component;
use function view;

class OnDemand extends Component
{
    public function render()
    {
        return view('sytatsu.on-demand')
            ->layout('layouts.sytatsu-layout', [
                'title' => 'On Demand',
            ]);
    }
}

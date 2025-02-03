<?php

namespace App\Http\Livewire\Sytatsu\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactForm extends Component
{

    // --------------- [FORM VARIABLES & FUNCTIONS] --------------- //
    #[Validate('required')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $message = '';

    public function send()
    {
        $isValid = $this->validate();

        if ($isValid) {
            $hasBeenSend = true;
        }
    }

    // --------------- [COMPONENT VARIABLES & FUNCTIONS] --------------- //
    public bool $hasBeenSend = false;

    public function mount()
    {
        //
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.contact-form');
    }
}

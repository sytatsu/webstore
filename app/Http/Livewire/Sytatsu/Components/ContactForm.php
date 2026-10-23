<?php

namespace App\Http\Livewire\Sytatsu\Components;

use Exception;
use App\Http\Livewire\Sytatsu\Concerns\StoresContactSubmissions;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactForm extends Component
{
    use StoresContactSubmissions;

    // --------------- [FORM VARIABLES & FUNCTIONS] --------------- //
    #[Validate('required', as: 'name')]
    public string $name = '';

    #[Validate('required|email', as: 'email address')]
    public string $email = '';

    #[Validate('nullable|max:20', as: 'phone number')]
    public string $phone = '';

    #[Validate('required|min:30', as: 'message')]
    public string $details = '';

    /**
     * @throws Exception
     */
    public function send(): void
    {
        $validatedArray = $this->validate();

        $validatedArray['service_type'] = 'contact';

        $this->storeAndSend($validatedArray);
    }

    // --------------- [COMPONENT VARIABLES & FUNCTIONS] --------------- //
    public bool $hasBeenSend = false;

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.contact-form');
    }
}

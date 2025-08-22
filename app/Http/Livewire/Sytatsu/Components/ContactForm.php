<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Mail\Sytatsu\ContactFormConfirmation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactForm extends Component
{

    // --------------- [FORM VARIABLES & FUNCTIONS] --------------- //
    #[Validate('required')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('max:11')]
    public string $phone = '';

    #[Validate('required|min:30')]
    public string $details = '';

    /**
     * @throws \Exception
     */
    public function send(): void
    {
        $validatedArray = $this->validate();

        if (!$validatedArray) {
            throw new \Exception('Something went wrong while validating the form, please try again.', 500);
        }

        Mail::to($this->email)
            ->send(mailable: new ContactFormConfirmation(
                data: $validatedArray
            ));

        $this->hasBeenSend = true;
    }

    // --------------- [COMPONENT VARIABLES & FUNCTIONS] --------------- //
    public bool $hasBeenSend = false;

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.contact-form');
    }
}

<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Mail\Sytatsu\Contact;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MaintenanceRepairForm extends Component
{
    #[Validate('required')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('max:11')]
    public string $phone = '';

    #[Validate('required')]
    public string $service_type = 'maintenance'; // maintenance, repair

    #[Validate('required|min:30')]
    public string $details = '';

    public bool $hasBeenSend = false;

    public function mount(string $service_type = 'maintenance'): void
    {
        $this->service_type = $service_type;
    }

    /**
     * @throws \Exception
     */
    public function send(): void
    {
        $validatedArray = $this->validate();

        if (!$validatedArray) {
            throw new \Exception('Something went wrong while validating the form, please try again.', 500);
        }

        $validatedArray['priority'] = 'normal';

        Mail::to($this->email)
            ->send(mailable: new Contact\Confirmation(
                data: $validatedArray
            ));

        $this->hasBeenSend = true;
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.maintenance-repair-form');
    }
}

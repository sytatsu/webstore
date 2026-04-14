<?php

namespace App\Mail\Sytatsu\Contact;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Confirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected array $data,
    ) {
        //
    }

    public function envelope(): Envelope
    {
        $type = $this->data['service_type'] ?? 'contact';
        $typeString = match($type) {
            'custom_print' => 'Custom Print Request',
            'maintenance' => 'Maintenance Request',
            'repair' => 'Repair Request',
            default => 'Contact form',
        };

        return new Envelope(
            from: new Address(
                address: config('mail.sytatsu.from.address'),
                name: config('mail.sytatsu.from.name'),
            ),
            bcc: [
                new Address(
                    address: config('mail.sytatsu.bcc.address'),
                    name: config('mail.sytatsu.bcc.name'),
                )
            ],
            replyTo: $this->data['email'],
            subject: "Sytatsu.nl | " . __($typeString),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.sytatsu.contact.confirmation',
            with: [
                'name'         => $this->data['name'],
                'email'        => $this->data['email'],
                'phone'        => $this->data['phone'] ?? '',
                'details'      => $this->data['details'],
                'service_type' => $this->data['service_type'] ?? 'contact',
            ]
        );
    }
}

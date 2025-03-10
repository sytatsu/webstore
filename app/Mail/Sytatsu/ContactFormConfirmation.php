<?php

namespace App\Mail\Sytatsu;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected array $data,
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address( // @TODO: From address should be configured within the config
                address: 'noreply@sytatsu.nl',
                name: 'Sytatsu'
            ),
            bcc: [
                new Address( // @TODO: BCC address should be configured within the config
                    address: 'info@sytatsu.nl',
                    name: 'Sytatsu'
                )
            ],
            subject: "Sytatsu.nl | Contact form",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.sytatsu.contact-form-confirmation',
            with: [
                'name'    => $this->data['name'],
                'email'   => $this->data['email'],
                'phone'   => $this->data['phone'] ?? '',
                'details' => $this->data['details'],
            ]
        );
    }
}

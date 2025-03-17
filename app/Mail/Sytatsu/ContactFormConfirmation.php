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

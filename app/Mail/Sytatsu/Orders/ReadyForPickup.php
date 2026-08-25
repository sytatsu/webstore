<?php

namespace App\Mail\Sytatsu\Orders;

use App\Models\PickupLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Lunar\Models\Order;

class ReadyForPickup extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public ?string $content;
    public ?PickupLocation $pickupLocation;

    public function __construct(Order $order, ?string $content) {
        $this->order = $order;
        $this->content = $content;
        $this->pickupLocation = $this->resolvePickupLocation($order);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                address: config('mail.sytatsu.from.address'),
                name: config('mail.sytatsu.from.name'),
            ),
            to: $this->order->shippingAddress->contact_email,
            subject: "Sytatsu.nl | Order ready for pick-up #{$this->order->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.sytatsu.orders.ready-for-pickup');
    }

    private function resolvePickupLocation(Order $order): ?PickupLocation
    {
        $pickupLocationId = $order->lines
            ->firstWhere('type', 'shipping')
            ?->meta['pickup_location_id'] ?? null;

        return $pickupLocationId ? PickupLocation::find($pickupLocationId) : null;
    }
}

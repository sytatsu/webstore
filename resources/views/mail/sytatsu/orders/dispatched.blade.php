@extends('mail.sytatsu.base')

@section('content')
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-top: 16px; margin-bottom: 0;">Order Dispatched</h1>
    </div>

    <p style="margin-bottom: 12px; font-weight: 500; font-size: 18px; color: #000000; text-align: center;">
        Your order <strong style="text-decoration: underline;">#{{ $order->reference }}</strong> is on its way!
    </p>

    @if ($order->carrier && $order->tracking_number)
        @php
            $carrier = \App\Enums\ShippingCarrierEnum::tryFrom($order->carrier);
        @endphp
        <div style="background-color: #f1f5f9; padding: 16px; border-radius: 8px; margin-bottom: 24px; text-align: center;">
            <p style="margin: 0; font-size: 16px; color: #4b5563;">Shipment Tracking ({{ $carrier?->label() ?? $order->carrier }}):</p>
            @if ($carrier)
                <p style="margin: 8px 0 0 0; font-size: 18px; font-weight: bold;">
                    <a href="{{ $carrier->trackingUrl($order->tracking_number) }}" style="color: #E14C04; text-decoration: none;">{{ $order->tracking_number }}</a>
                </p>
            @else
                <p style="margin: 8px 0 0 0; font-size: 18px; font-weight: bold; color: #E14C04;">{{ $order->tracking_number }}</p>
            @endif
        </div>
    @endif

    @if ($content)
        <div style="background-color: #f1f5f9; padding: 16px; border-radius: 8px; margin-bottom: 24px; text-align: center;">
            <p style="margin: 0; font-size: 16px; color: #4b5563;">Track & Trace:</p>
            <p style="margin: 8px 0 0 0; font-size: 18px; font-weight: bold; color: #E14C04;">{{ $content }}</p>
        </div>
    @endif

    @if($order->shippingAddress)
        @php
            $shippingAddress = $order->shippingAddress;
            $billingAddress = $order->billingAddress;
        @endphp

        <div style="border-top: 1px solid #e2e8f0; padding-top: 24px; margin-top: 24px;">
            <h2 style="font-size: 20px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-bottom: 16px;">Delivery Address</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="vertical-align: top; padding-right: 16px;">
                        @include('mail.sytatsu.orders.includes.address', ['address' => $shippingAddress, 'label' => 'Shipping Address'])
                    </td>
                    <td style="vertical-align: top;">
                        @include('mail.sytatsu.orders.includes.address', ['address' => $billingAddress, 'label' => 'Billing Address'])
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div style="border-top: 1px solid #e2e8f0; padding-top: 24px; margin-top: 24px;">
        <h2 style="font-size: 20px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-bottom: 16px;">Order Summary</h2>
        @include('mail.sytatsu.orders.includes.order-table')
    </div>

    @include('mail.sytatsu.orders.includes.notes')
@endsection

@extends('mail.sytatsu.base')

@section('content')
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-top: 16px; margin-bottom: 0;">Order has been placed</h1>
    </div>

    <p style="margin-bottom: 24px; font-weight: 500; font-size: 18px; color: #000000; text-align: center;">
        Your order reference number is <strong style="text-decoration: underline;">#{{ $order->reference }}</strong>
    </p>

    @if($order->shippingAddress)
        @php
            $shippingAddress = $order->shippingAddress;
            $billingAddress = $order->billingAddress;
            $billingDiffers = $billingAddress && (
                $billingAddress->line_one !== $shippingAddress->line_one
                || $billingAddress->postcode !== $shippingAddress->postcode
                || $billingAddress->city !== $shippingAddress->city
            );
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
        <h2 style="font-size: 20px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-bottom: 16px;">Order Details</h2>
        @include('mail.sytatsu.orders.includes.order-table')
    </div>

    @include('mail.sytatsu.orders.includes.notes')
@endsection

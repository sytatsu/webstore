@extends('mail.sytatsu.base')

@section('content')
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-top: 16px; margin-bottom: 0;">Order has been placed</h1>
    </div>

    <p style="margin-bottom: 12px; font-weight: 500; font-size: 18px; color: #000000; text-align: center;">
        Your order reference number is <strong style="text-decoration: underline;">#{{ $order->reference }}</strong>
    </p>

    <p style="margin-bottom: 24px; font-size: 16px; color: #4b5563; text-align: center;">
        Thank you for your order! An email confirmation has been sent to you. Below you will find your order details.
    </p>

    <div style="border-top: 1px solid #e2e8f0; padding-top: 24px;">
        <h2 style="font-size: 20px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-bottom: 16px;">Order Details</h2>
        @include('mail.sytatsu.orders.includes.order-table')
    </div>
@endsection

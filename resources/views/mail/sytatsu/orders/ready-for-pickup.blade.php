@extends('mail.sytatsu.base')

@section('content')
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-top: 16px; margin-bottom: 0;">Ready for Pick-up</h1>
    </div>

    <p style="margin-bottom: 12px; font-weight: 500; font-size: 18px; color: #000000; text-align: center;">
        Your order <strong style="text-decoration: underline;">#{{ $order->reference }}</strong> is ready to be picked up!
    </p>

    @if ($pickupLocation)
        <div style="background-color: #f1f5f9; padding: 16px; border-radius: 8px; margin-bottom: 24px; text-align: center;">
            <p style="margin: 0; font-size: 16px; color: #4b5563;">Pick-up location:</p>
            <p style="margin: 8px 0 0 0; font-size: 18px; font-weight: bold; color: #1e293b;">{{ $pickupLocation->name }}</p>
            <p style="margin: 4px 0 0 0; color: #1e293b;">{{ $pickupLocation->address_line_1 }}</p>
            @if ($pickupLocation->address_line_2)
                <p style="margin: 0; color: #1e293b;">{{ $pickupLocation->address_line_2 }}</p>
            @endif
            <p style="margin: 0; color: #1e293b;">{{ $pickupLocation->postcode }} {{ $pickupLocation->city }}</p>
            <p style="margin: 0; color: #1e293b;">{{ $pickupLocation->country }}</p>
            @if ($pickupLocation->translate('availability_note'))
                <p style="margin: 12px 0 0 0; font-size: 14px; color: #64748b;">{{ $pickupLocation->translate('availability_note') }}</p>
            @endif
        </div>
    @endif

    @if ($content)
        <div style="background-color: #f1f5f9; padding: 16px; border-radius: 8px; margin-bottom: 24px; text-align: center;">
            <p style="margin: 0; font-size: 16px; color: #4b5563;">Additional information:</p>
            <p style="margin: 8px 0 0 0; font-size: 18px; font-weight: bold; color: #E14C04;">{{ $content }}</p>
        </div>
    @endif

    <div style="border-top: 1px solid #e2e8f0; padding-top: 24px; margin-top: 24px;">
        <h2 style="font-size: 20px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-bottom: 16px;">Order Summary</h2>
        @include('mail.sytatsu.orders.includes.order-table')
    </div>

    @include('mail.sytatsu.orders.includes.notes')
@endsection

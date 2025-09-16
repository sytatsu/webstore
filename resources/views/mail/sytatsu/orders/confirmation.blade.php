@extends('mail.sytatsu.base')

@section('content')
    <p style="margin-bottom: 10px; font-weight: normal; font-size:16px; color: #333333;">Order reference: #{{ $order->reference }}</p>
    <p style="margin-bottom: 10px; font-weight: normal; font-size:16px; color: #333333;">Thank you for your order! Underneath you will find a copy of your order.</p>

    @include('mail.sytatsu.orders.includes.order-table')
@endsection

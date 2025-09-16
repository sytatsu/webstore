@extends('mail.sytatsu.base')

@section('content')
    <p style="margin-bottom: 10px; font-weight: normal; font-size:16px; color: #333333;">Order reference: #{{ $order->reference }}</p>
    <p style="margin-bottom: 10px; font-weight: normal; font-size:16px; color: #333333;">Your order has been dispatched and is on it's way!</p>

    @if ($content)
        <p style="display: block; margin-bottom: 10px; font-weight: normal; font-size:16px; color: #333333;">Track & Trace: <b>{{ $content }}</b></p>
    @endif

    @include('mail.sytatsu.orders.includes.order-table')
@endsection

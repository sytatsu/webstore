@props(['order'])

<table style="margin: 0 auto; margin-bottom: 20px;">
    <tr style="background-color: #eeeeee;">
        <th style="font-size:13px;font-weight:bold;text-align:left;padding: 3px 6px;" width="70%">Item</th>
        <th style="font-size:13px;font-weight:bold;text-align:right;padding: 3px 6px;" width="30%" align="right">Amount</th>
        <th style="font-size:13px;font-weight:bold;text-align:right;padding: 3px 6px;" width="30%" align="right">Total</th>
    </tr>

    @foreach($order->lines as $orderLine)
        @if($orderLine->purchasable_type === 'product_variant')
            <tr style="border-bottom: solid 1px #f7f7f7;">
                <td style="font-size:13px;font-weight:normal;padding: 0px 6px;vertical-align:top;">{{ $orderLine->purchasable->product->translateAttribute('name') }}</td>
                <td style="font-size:13px;font-weight:normal;padding: 0px 6px;vertical-align:top;" align="right">{{ $orderLine->quantity }}</td>
                <td style="font-size:13px;font-weight:normal;padding: 0px 6px;vertical-align:top;" align="right">{{ $orderLine->sub_total->formatted() }}</td>
            </tr>
        @endif
    @endforeach

    {{-- @TODO; need to find a way to remove double foreach loop--}}
    @foreach($order->lines as $orderLine)
        @if($orderLine->purchasable_type === \Lunar\DataTypes\ShippingOption::class)
            <tr style="border-bottom: solid 1px #f7f7f7;">
                <td style="font-size:13px;font-weight:normal;padding: 0px 6px;vertical-align:top; text-align: right">{{ $orderLine->description}}</td>
                <td style="font-size:13px;font-weight:normal;padding: 0px 6px;vertical-align:top;" align="right">{{ $orderLine->quantity }}</td>
                <td style="font-size:13px;font-weight:normal;padding: 0px 6px;vertical-align:top;" align="right">{{ $orderLine->sub_total->formatted() }}</td>
            </tr>
        @endif
    @endforeach
</table>
<table style="width: 40%!important; margin-left: 60%; margin-bottom: 20px;">
    <tr>
        <td style="font-size:13px;font-weight:normal;padding: 0px 6px;vertical-align:top;" align="right">Order total:</td>
        <td style="font-size:13px;font-weight:normal;padding: 0px 6px;vertical-align:top;" align="right">{{ $order->total->formatted() }}</td>
    </tr>
</table>

@props(['order'])

<table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
    <thead>
        <tr style="border-bottom: 2px solid #e2e8f0;">
            <th style="text-align: left; padding: 12px 8px; font-size: 14px; text-transform: uppercase; color: #64748b;">Item</th>
            <th style="text-align: center; padding: 12px 8px; font-size: 14px; text-transform: uppercase; color: #64748b;">Qty</th>
            <th style="text-align: right; padding: 12px 8px; font-size: 14px; text-transform: uppercase; color: #64748b;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->lines as $orderLine)
            @if($orderLine->purchasable_type !== \Lunar\DataTypes\ShippingOption::class)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px 8px; vertical-align: middle;">
                        <div style="font-weight: bold; color: #1e293b;">
                            @if($orderLine->purchasable && $orderLine->purchasable->product)
                                <a href="{{ route('sytatsu.webstore.product', $orderLine->purchasable->product->defaultUrl->slug) }}" style="color: #E14C04; text-decoration: none;">{{ $orderLine->description }}</a>
                            @else
                                {{ $orderLine->description }}
                            @endif
                        </div>
                        @if($orderLine->option)
                            <div style="font-size: 12px; color: #64748b; font-style: italic;">{{ $orderLine->option }}</div>
                        @endif
                    </td>
                    <td style="padding: 12px 8px; text-align: center; color: #1e293b; vertical-align: middle;">{{ $orderLine->quantity }}</td>
                    <td style="padding: 12px 8px; text-align: right; font-weight: bold; color: #1e293b; vertical-align: middle;">{{ $orderLine->sub_total->formatted() }}</td>
                </tr>
                @if($barBuilder = ($orderLine->meta['bar_builder'] ?? null))
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td colspan="3" style="padding: 0 8px 12px 8px;">
                            @include('mail.sytatsu.orders.includes.bar-builder-details', ['barBuilder' => $barBuilder])
                        </td>
                    </tr>
                @endif
            @endif
        @endforeach
    </tbody>
</table>

<div style="margin-left: auto; width: 100%; max-width: 240px; border-top: 1px solid #e2e8f0; padding-top: 12px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 4px 8px; color: #64748b;">Subtotal</td>
            <td style="padding: 4px 8px; text-align: right; color: #1e293b;">{{ $order->sub_total->formatted() }}</td>
        </tr>
        @foreach($order->lines as $orderLine)
            @if($orderLine->purchasable_type === \Lunar\DataTypes\ShippingOption::class)
                <tr>
                    <td style="padding: 4px 8px; color: #64748b;">Shipping</td>
                    <td style="padding: 4px 8px; text-align: right; color: #1e293b;">{{ $orderLine->sub_total->formatted() }}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td style="padding: 4px 8px; color: #64748b;">Tax</td>
            <td style="padding: 4px 8px; text-align: right; color: #1e293b;">{{ $order->tax_total->formatted() }}</td>
        </tr>
        <tr style="font-size: 18px; font-weight: bold;">
            <td style="padding: 12px 8px; color: #1C315E; text-transform: uppercase;">Total</td>
            <td style="padding: 12px 8px; text-align: right; color: #1C315E;">{{ $order->total->formatted() }}</td>
        </tr>
    </table>
</div>

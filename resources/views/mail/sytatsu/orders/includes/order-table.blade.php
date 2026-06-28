@props(['order'])

@php
    $productLines = $order->lines->filter(fn($l) => $l->purchasable_type !== \Lunar\DataTypes\ShippingOption::class);
    $bundleGroups = $productLines->filter(fn($l) => !empty(data_get($l->meta, 'bundle_id')))->groupBy(fn($l) => data_get($l->meta, 'bundle_id'));
    $regularLines = $productLines->filter(fn($l) => empty(data_get($l->meta, 'bundle_id')));

    $bundleConfigIds = $bundleGroups->map(fn($g) => data_get($g->first()->meta, 'bundle_config_id'))->filter()->unique()->values();
    $bundleConfigs = \App\Models\BundleConfig::findMany($bundleConfigIds)->keyBy('id');
@endphp

<table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
    <thead>
        <tr style="border-bottom: 2px solid #e2e8f0;">
            <th style="text-align: left; padding: 12px 8px; font-size: 14px; text-transform: uppercase; color: #64748b;">Item</th>
            <th style="text-align: center; padding: 12px 8px; font-size: 14px; text-transform: uppercase; color: #64748b;">Qty</th>
            <th style="text-align: right; padding: 12px 8px; font-size: 14px; text-transform: uppercase; color: #64748b;">Total</th>
        </tr>
    </thead>
    <tbody>
        {{-- Bundle groups --}}
        @foreach ($bundleGroups as $bundleId => $bundleLines)
            @php
                $firstMeta = $bundleLines->first()->meta;
                $discountPct = data_get($firstMeta, 'bundle_discount_pct', 0);
                $bundleConfigId = data_get($firstMeta, 'bundle_config_id');
                $bundleLabel = data_get($firstMeta, 'bundle_name')
                    ?: (($bundleConfigId && $bundleConfigs->has($bundleConfigId))
                        ? ($bundleConfigs->get($bundleConfigId)->getTranslatedName() ?: 'Bundle')
                        : 'Bundle');
            @endphp
            <tr>
                <td colspan="3" style="padding: 10px 8px 4px; background-color: #f8fafc;">
                    <span style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">
                        {{ $bundleLabel }}
                    </span>
                    @if ($discountPct > 0)
                        <span style="margin-left: 6px; background-color: #E14C04; color: #ffffff; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 3px;">
                            -{{ number_format($discountPct, 0) }}%
                        </span>
                    @endif
                </td>
            </tr>
            @foreach ($bundleLines as $orderLine)
                <tr style="border-bottom: 1px solid #f1f5f9; background-color: #fafafa;">
                    <td style="padding: 10px 8px 10px 20px; vertical-align: middle;">
                        <div style="font-weight: bold; color: #1e293b;">
                            @if ($orderLine->purchasable && $orderLine->purchasable->product)
                                <a href="{{ route('sytatsu.webstore.product', $orderLine->purchasable->product->defaultUrl->slug) }}" style="color: #E14C04; text-decoration: none;">{{ $orderLine->description }}</a>
                            @else
                                {{ $orderLine->description }}
                            @endif
                        </div>
                        @if ($orderLine->option)
                            <div style="font-size: 12px; color: #64748b; font-style: italic;">{{ $orderLine->option }}</div>
                        @endif
                    </td>
                    <td style="padding: 10px 8px; text-align: center; color: #1e293b; vertical-align: middle;">{{ $orderLine->quantity }}</td>
                    <td style="padding: 10px 8px; text-align: right; font-weight: bold; color: #1e293b; vertical-align: middle;">{{ $orderLine->sub_total->formatted() }}</td>
                </tr>
            @endforeach
        @endforeach

        {{-- Regular lines --}}
        @foreach ($regularLines as $orderLine)
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 12px 8px; vertical-align: middle;">
                    <div style="font-weight: bold; color: #1e293b;">
                        @if ($orderLine->purchasable && $orderLine->purchasable->product)
                            <a href="{{ route('sytatsu.webstore.product', $orderLine->purchasable->product->defaultUrl->slug) }}" style="color: #E14C04; text-decoration: none;">{{ $orderLine->description }}</a>
                        @else
                            {{ $orderLine->description }}
                        @endif
                    </div>
                    @if ($orderLine->option)
                        <div style="font-size: 12px; color: #64748b; font-style: italic;">{{ $orderLine->option }}</div>
                    @endif
                </td>
                <td style="padding: 12px 8px; text-align: center; color: #1e293b; vertical-align: middle;">{{ $orderLine->quantity }}</td>
                <td style="padding: 12px 8px; text-align: right; font-weight: bold; color: #1e293b; vertical-align: middle;">{{ $orderLine->sub_total->formatted() }}</td>
            </tr>
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

@if($order->notes)
    <div style="border-top: 1px solid #e2e8f0; padding-top: 24px; margin-top: 24px;">
        <h2 style="font-size: 20px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-bottom: 16px;">Order Notes</h2>
        <p style="margin: 0; color: #1e293b; white-space: pre-line;">{{ $order->notes }}</p>
    </div>
@endif

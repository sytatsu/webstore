@props(['bundle'])

<div style="margin-top: 4px; margin-bottom: 4px; padding: 10px 12px; background-color: #f8fafc; border-radius: 8px;">
    <div style="font-size: 12px; color: #64748b; margin-bottom: 8px;">
        <strong style="color: #1e293b;">{{ $bundle['name'] ?? __('Bundle') }}</strong>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        @foreach($bundle['items'] ?? [] as $item)
            <tr>
                <td style="padding: 2px 0; vertical-align: middle; width: 32px;">
                    @if($item['thumbnail'] ?? null)
                        <img src="{{ url($item['thumbnail']) }}" width="28" height="28" alt="" style="width: 28px; height: 28px; object-fit: cover; border-radius: 6px; display: block;">
                    @endif
                </td>
                <td style="padding: 2px 0 2px 8px; font-size: 12px; color: #1e293b;">
                    {{ $item['name'] ?? '' }}
                </td>
                <td style="padding: 2px 0; font-size: 12px; color: #64748b; text-align: right;">
                    &times; {{ $item['quantity'] ?? 1 }}
                </td>
            </tr>
        @endforeach
    </table>
</div>

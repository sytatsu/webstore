@props(['barBuilder'])

<div style="margin-top: 4px; margin-bottom: 4px; padding: 10px 12px; background-color: #f8fafc; border-radius: 8px;">
    <div style="font-size: 12px; color: #64748b; margin-bottom: 8px;">
        <strong style="color: #1e293b;">Text:</strong> &ldquo;{{ $barBuilder['text'] ?? '' }}&rdquo;
        &nbsp;&middot;&nbsp;
        <strong style="color: #1e293b;">Base colour:</strong> {{ $barBuilder['base_colour']['name'] ?? '—' }}
        &nbsp;&middot;&nbsp;
        <strong style="color: #1e293b;">Ref:</strong> {{ $barBuilder['reference'] ?? '—' }}
    </div>

    <table style="border-collapse: separate; border-spacing: 4px 0;">
        <tr>
            @foreach($barBuilder['caps'] ?? [] as $cap)
                <td style="width: 30px; height: 30px; min-width: 30px; text-align: center; vertical-align: middle; border-radius: 6px; background-color: {{ $cap['colour']['hex'] ?? '#e2e8f0' }}; color: {{ $cap['text_colour']['hex'] ?? '#1e293b' }}; font-weight: bold; font-size: 12px;">
                    @if($cap['icon'] ?? null)
                        <span style="font-size: 8px; font-weight: normal;">{{ $cap['icon']['name'] }}</span>
                    @else
                        {{ $cap['character'] ?? '' }}
                    @endif
                </td>
            @endforeach
        </tr>
    </table>
</div>

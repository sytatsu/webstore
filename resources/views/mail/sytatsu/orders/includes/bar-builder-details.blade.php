@props(['barBuilder'])

@php
    $iconIds = collect($barBuilder['caps'] ?? [])->pluck('icon.id')->filter()->unique()->values();
    $icons = $iconIds->isNotEmpty()
        ? \App\Models\BarBuilderIcon::query()->whereIn('id', $iconIds)->get()->keyBy('id')
        : collect();

    // Email clients can't reliably render inline <svg>, so each icon glyph is
    // rasterised into a small data-URI image built from its stored SVG path
    // (same cx/cy/scale math the live builder preview uses).
    $iconImageSrc = function (?array $icon, string $color) use ($icons) {
        $model = $icon ? $icons->get($icon['id'] ?? null) : null;

        if (!$model) {
            return null;
        }

        $tx = round(50 - $model->scale * $model->cx, 2);
        $ty = round(50 - $model->scale * $model->cy, 2);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="16" height="16">'
            . '<g transform="translate(' . $tx . ' ' . $ty . ') scale(' . $model->scale . ')">'
            . '<path d="' . e($model->path) . '" fill="' . e($color) . '"/>'
            . '</g></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    };
@endphp

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
                        @php $iconSrc = $iconImageSrc($cap['icon'], $cap['text_colour']['hex'] ?? '#1e293b'); @endphp
                        @if($iconSrc)
                            <img src="{{ $iconSrc }}" width="16" height="16" alt="{{ $cap['icon']['name'] ?? '' }}" style="display: inline-block; vertical-align: middle;">
                        @else
                            <span style="font-size: 8px; font-weight: normal;">{{ $cap['icon']['name'] ?? '' }}</span>
                        @endif
                    @else
                        {{ $cap['character'] ?? '' }}
                    @endif
                </td>
            @endforeach
        </tr>
    </table>
</div>

@props(['barBuilder', 'icons'])

<div>
    <div style="display: flex; flex-wrap: wrap; gap: 8px 24px; font-size: 13px; margin-bottom: 16px;">
        <div>
            <strong>{{ __('Text') }}:</strong>
            &ldquo;{{ $barBuilder['text'] ?? '' }}&rdquo;
        </div>

        <div style="display: inline-flex; align-items: center; gap: 6px;">
            <strong>{{ __('Base colour') }}:</strong>
            <span style="display: inline-block; width: 14px; height: 14px; border-radius: 9999px; border: 1px solid rgba(127, 127, 127, .5); background: {{ $barBuilder['base_colour']['hex'] ?? 'transparent' }};"></span>
            {{ $barBuilder['base_colour']['name'] ?? '—' }}
        </div>

        <div><strong>{{ __('Reference') }}:</strong> {{ $barBuilder['reference'] ?? '—' }}</div>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 14px;">
        @foreach($barBuilder['caps'] ?? [] as $cap)
            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; width: 56px;" wire:key="bar-builder-cap-{{ $loop->index }}">
                <div style="width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; border: 1px solid rgba(127, 127, 127, .35); box-shadow: 0 1px 2px rgba(0, 0, 0, .25); background: {{ $cap['colour']['hex'] ?? '#e5e7eb' }}; color: {{ $cap['text_colour']['hex'] ?? '#111827' }};">
                    @if($cap['icon'] ?? null)
                        @php $icon = $icons->get($cap['icon']['id']); @endphp
                        @if($icon)
                            <svg viewBox="0 0 100 100" width="24" height="24" aria-hidden="true" style="display: block;">
                                <path d="{{ $icon->path }}" fill="currentColor"></path>
                            </svg>
                        @else
                            <span style="font-size: 9px; line-height: 1.1; text-align: center; font-weight: 600;">{{ $cap['icon']['name'] }}</span>
                        @endif
                    @else
                        {{ $cap['character'] ?? '' }}
                    @endif
                </div>
                <span style="font-size: 10px; color: #6b7280;">#{{ $cap['position'] ?? $loop->iteration }}</span>
                <span style="font-size: 10px; color: #6b7280; text-align: center;">{{ $cap['combination'] ?? '' }}</span>
            </div>
        @endforeach
    </div>
</div>

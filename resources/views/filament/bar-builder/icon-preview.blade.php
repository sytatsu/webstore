@props(['paths', 'cx', 'cy', 'scale'])

@if(empty($paths))
    <span class="text-sm text-gray-400">{{ __('Enter path data above to see a preview.') }}</span>
@else
    <div style="width: 96px; height: 96px; border-radius: 16px; background: #1B1D21; display: flex; align-items: center; justify-content: center;">
        <svg viewBox="0 0 96 96" width="96" height="96" aria-hidden="true">
            <g transform="translate({{ 48 - $scale * $cx }} {{ 48 - $scale * $cy }}) scale({{ $scale }})">
                <path d="{{ implode(' ', $paths) }}" fill="#ffffff"></path>
            </g>
        </svg>
    </div>
@endif

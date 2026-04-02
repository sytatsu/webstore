@props([
    'buttonType' => 'default',
    'classes' => '',
])

@php
    $defaultClasses = implode(' ', [match ($buttonType) {
        'outline'   => 'rounded-none shadow-sm',
        'link'      => '',
        default     => 'rounded-none shadow-sm hover:shadow-md active:shadow-inner active:translate-y-px',
    }, "block py-3 px-10 text-xs font-bold avenir-bold uppercase tracking-widest text-center outline-none transition-all duration-200 hover:cursor-pointer disabled:opacity-50 disabled:pointer-events-none"]);
@endphp

@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => implode(' ', [$classes, $defaultClasses])]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => implode(' ', [$classes, $defaultClasses])]) }}>
        {{ $slot }}
    </button>
@endif

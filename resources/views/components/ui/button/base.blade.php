@props([
    'buttonType' => 'default',
    'classes' => '',
])

@php
    $defaultClasses = implode(' ', [match ($buttonType) {
        'outline'   => 'rounded-xl shadow-sm',
        'link'      => '',
        default     => 'rounded-xl shadow-sm hover:shadow-md active:shadow-inner active:translate-y-px',
    }, "block py-3 px-4 md:px-6 lg:px-8 xl:px-10 text-xs font-bold avenir-bold uppercase tracking-widest text-center outline-none transition-all duration-200 hover:cursor-pointer disabled:opacity-50 disabled:pointer-events-none"]);
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

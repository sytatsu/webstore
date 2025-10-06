@props([
    'buttonType' => 'default',
    'classes' => '',
])

@php
    $defaultClasses = implode(' ', [match ($buttonType) {
        'outline'   => 'rounded-lg shadow',
        'link'      => '',
        default     => 'rounded-lg shadow hover:shadow-none inset-shadow-sm inset-shadow-transparent hover:inset-shadow-gray-700',
    }, "block py-3 px-5 text-sm font-medium text-center outline-none transition-all duration-300 hover:cursor-pointer"]);
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

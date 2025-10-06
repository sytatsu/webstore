@props([
    'label',
    'inputType' => 'input',
    'classes' => '',
    'size' => 'full',
    'parentClasses' => '',
])

@php
    $defaultClasses = implode(' ', [match($inputType) {
        'textarea' => '',
        'select' => '',
        'input' => '',
    }, 'py-2 px-3 block bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600']);

    $sizeClasses = match ($size) {
        'full' => 'w-full'
    };

    $errorClasses = $errors->has($attributes->get('id')) ? '!border-red-500' : '';

    $mergedClasses = implode(' ', [$classes, $sizeClasses, $defaultClasses, $errorClasses]);
@endphp

<div class="{{ $parentClasses }}">
    <label for="{{ $attributes->get('id') }}" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
        @if ($attributes->has('required'))
            <span class="text-red-500">*</span>
        @endif
        {{ __($label) }}
    </label>

    @switch($inputType)
        @case('textarea')
            <textarea {{ $attributes->merge(['class' => $mergedClasses]) }}>{{ $slot }}</textarea>
        @break

        @case('select')
            <select {{ $attributes->merge(['class' => $mergedClasses]) }}>
                {{ $slot }}
            </select>
        @break

        @default
            <input {{ $attributes->merge(['class' => $mergedClasses]) }} />
        @break
    @endswitch

    @if ($errors->has($attributes->get('id')))
        {{-- TODO; Might want to find another way, but show first only for now --}}
        <span class="block mt-0.25 h-0 text-red-500 text-xs">{{ $errors->get($attributes->get('id'))[0] }}</span>
    @endif
</div>

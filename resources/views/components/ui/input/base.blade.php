@props([
    'label' => null,
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
    }, 'py-3 px-4 block w-full bg-gray-50 border-gray-300 rounded-none text-sm focus:border-primary focus:ring-primary disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-slate-700 dark:text-gray-400 dark:placeholder-gray-500 dark:focus:ring-slate-600 transition-all duration-200']);

    $sizeClasses = match ($size) {
        'full' => 'w-full'
    };

    $errorClasses = $errors->has($attributes->get('id')) ? '!border-red-500' : '';

    $mergedClasses = implode(' ', [$classes, $sizeClasses, $defaultClasses, $errorClasses]);
@endphp

<div class="{{ $parentClasses }}">
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block mb-2 text-xs text-gray-700 font-bold dark:text-white avenir-bold uppercase tracking-widest">
            @if ($attributes->has('required'))
                <span class="text-red-500">*</span>
            @endif
            {{ __($label) }}
        </label>
    @endif

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

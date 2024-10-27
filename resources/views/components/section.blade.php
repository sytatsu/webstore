@props([
    'width' => 'max-w-xl'
])
<section {{ $attributes->merge(['class' => 'p-4 sm:p-8 bg-white shadow sm:rounded-lg']) }}>
    <div class="{{ $width }}">
        {{ $slot }}
    </div>
</section>

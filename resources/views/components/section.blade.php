@props([
    'width' => 'max-w-xl',
    'innerClass' => ''
])
<section {{ $attributes->exceptProps(['innerClass', 'width'])->merge(['class' => 'p-6 sm:p-8 bg-white shadow sm:rounded-lg']) }}>
    <div class="{{ $width }} {{ $innerClass }}">
        {{ $slot }}
    </div>
</section>

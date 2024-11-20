@props([
    'width' => 'max-w-xl',
    'innerClass' => ''
])
<section {{ $attributes->exceptProps(['innerClass', 'width'])->merge(['class' => 'p-4 sm:p-6 bg-white shadow sm:rounded-lg']) }}>
    <div class="{{ $width }} {{ $innerClass }}">
        {{ $slot }}
    </div>
</section>

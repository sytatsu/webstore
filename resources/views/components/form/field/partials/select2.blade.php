@props([
    'position' => 'bottom'
])

@php($fieldOpenPositionClasses = match ($position) {
        'top'    => 'rounded-b-lg',
        'bottom' => 'rounded-t-lg',
    })

@php($listPositionClasses = match ($position) {
        'top'    => 'bottom-0 mb-12 border border-b-0 rounded-t-lg',
        'bottom' => 'top-0 mt-12 border border-t-0 rounded-b-lg',
    })

<div x-data="{
            options: {{ json_encode($attributes->get('selected')) }},
            open: false,
        }" class="mt-1 w-full relative">
    <div class="p-3 flex gap-2 w-full border border-neutral-300 cursor-pointer truncate h-12 bg-white"
         x-bind:class="open ? '{{ $fieldOpenPositionClasses }}' : 'rounded-lg'"
         @click="open = !open"
         x-text="options.length + ' items selected'">
        {{-- a moment of silence for this empty div --}}
    </div>
    <div class="p-3 absolute w-full flex gap-3 x-50 flex-col border-gray-300 bg-white {{ $listPositionClasses }}"
         x-show="open" x-trap="open"
         @click.outside="open = false"
         @keydown.escape.window="open = false"
    >
        @foreach($attributes->get('options') as $optionKey => $optionValue)
            <div class="flex items-center">
                <input x-model="options" id="product_variant.variants.{{ $loop->index }}" name="product_variant.variants[]" type="checkbox" value="{{ $optionKey }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                <label for="product_variant.variants.{{ $loop->index }}" class="ml-2 text-sm font-medium text-gray-900">{{ $optionValue }}</label>
            </div>
        @endforeach
    </div>
</div>

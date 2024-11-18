<div class="mt-1" x-data="{
            options: {{ json_encode($attributes->get('selected')) }},
            open: false,
        }" class="w-full relative">
    <div @click="open = !open" x-bind:class="open ? 'rounded-t-lg' : 'rounded-lg'" class="p-3 flex gap-2 w-full border border-neutral-300 cursor-pointer truncate h-12 bg-white" x-text="options.length + ' items selected'">
        {{-- a moment of silence for this empty div --}}
    </div>
    <div class="p-3 rounded-b-lg flex gap-3 w-full shadow-lg x-50 flex-col border border-t-0 border-gray-300 bg-white" x-show="open" x-trap="open" @click.outside="open = false" @keydown.escape.window="open = false">
        @foreach($attributes->get('options') as $optionKey => $optionValue)
            <div class="flex items-center">
                <input x-model="options" id="product_variant.variants.{{ $loop->index }}" name="product_variant.variants[]" type="checkbox" value="{{ $optionKey }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                <label for="product_variant.variants.{{ $loop->index }}" class="ml-2 text-sm font-medium text-gray-900">{{ $optionValue }}</label>
            </div>
        @endforeach
    </div>
</div>

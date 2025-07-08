<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
    {{-- @TODO; Add conditional title/label --}}
    @if ($this->label)
        <h2 class="text-2xl pb-5 avenir-bold text-black dark:text-white">{{ $this->label }}</h2>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
        {{-- @TODO; Should be within a foreach loop to make more than just one group (dataTransformer/Service?) --}}
        @foreach($products as $product)
            <livewire:sytatsu.components.product.product-tile :product="$product" />
        @endforeach
    </div>
</div>

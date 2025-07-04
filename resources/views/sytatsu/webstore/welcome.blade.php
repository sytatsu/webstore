<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
        @foreach($this->getProducts() as $product)
            <livewire:sytatsu.components.product.product-tile :product="$product" />
        @endforeach
    </div>
</div>

<div class="flex flex-col gap-4">
    <x-section-header>
        {{ __("Details") }}
    </x-section-header>

    @include('warehouse.product.partials.product-variant-show', ['productVariant' => $productVariant])
</div>

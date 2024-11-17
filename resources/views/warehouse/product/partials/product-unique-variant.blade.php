<div class="flex flex-col gap-4">
    <div class="flex flex-row justify-between bg-slate-700 text-white rounded-lg p-4">
        <span class='block avenir-bold text-lg my-auto ml-2'>
            {{ __("Details") }}
        </span>
    </div>

    @include('warehouse.product.partials.product-variant-show', ['productVariant' => $productVariant])
</div>

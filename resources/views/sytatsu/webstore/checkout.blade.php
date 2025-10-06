<div class="min-h-screen grid grid-cols-3">
    <div class="col-span-2 p-8 flex items-center justify-center max-h-screen overflow-y-scroll shadow-md">
        <livewire:sytatsu.components.checkout-form/>
    </div>

    <div class="col-span-1 p-8 flex items-center justify-items-stretch bg-slate-50 dark:bg-slate-800">
        {{-- TODO: create new cart component with same style but different functionality--}}
        <livewire:sytatsu.components.cart.cart-details :checkout="true"/>
    </div>
</div>

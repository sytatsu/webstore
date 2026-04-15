<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full flex flex-col gap-8">
    <div class="flex flex-col md:grid md:grid-cols-3 gap-8 md:gap-12">
        <div class="md:col-span-2 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 self-start w-full relative"
             x-data="{ stripeProcessing: false }"
             x-on:stripe-processing.window="stripeProcessing = $event.detail"
        >
            <x-ui.spinner-overlay x-show="stripeProcessing" />


            <livewire:sytatsu.components.checkout-form/>
        </div>

        <div class="md:col-span-1 flex flex-col gap-8">
            <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12">
                <livewire:sytatsu.components.cart.details :checkout="true"/>
            </div>

            <x-sytatsu.sidebar.help />
        </div>
    </div>
</div>

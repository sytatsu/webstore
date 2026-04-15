<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full flex flex-col gap-8">
    <div class="flex flex-col md:grid md:grid-cols-3 gap-8 md:gap-12">
        <div class="md:col-span-2 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 self-start w-full relative"
             x-data="{ stripeProcessing: false }"
             x-on:stripe-processing.window="stripeProcessing = $event.detail"
        >
            <div
                x-show="stripeProcessing"
                style="display: none;"
                class="absolute inset-0 z-50 flex items-center justify-center bg-white/50 dark:bg-slate-800/50"
            >
                <svg class="w-12 h-12 text-primary animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>


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

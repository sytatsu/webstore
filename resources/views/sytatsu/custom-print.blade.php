<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full flex flex-col gap-8">
    <div class="flex flex-col md:grid md:grid-cols-3 gap-8 md:gap-12">
        <div class="md:col-span-2 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 self-start w-full">
            <x-ui.page-header tag="h1" title="{{ __('Custom Print Request') }}" />

            <p class="mb-8 text-gray-600 dark:text-neutral-400">
                {{ __('Have a specific design in mind? We can print it for you in high quality with various materials and colors. Please fill out the form below and we will get back to you with a quote.') }}
            </p>

            <livewire:sytatsu.components.print-request-form />
        </div>

        <div class="md:col-span-1 flex flex-col gap-8">
            <x-ui.card-box title="{{ __('Why Custom Print?') }}" class="text-sm">
                <div class="space-y-3 text-gray-600 dark:text-neutral-400">
                    <p>
                        {{ __('Custom 3D printing allows you to bring unique ideas to life. Whether it\'s a replacement part, a personalized gift, or a complex prototype, we have the tools to make it happen.') }}
                    </p>
                </div>
            </x-ui.card-box>

            <x-sytatsu.sidebar.help />
        </div>
    </div>
</div>

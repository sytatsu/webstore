<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full flex flex-col gap-8">
    <div class="flex flex-col md:grid md:grid-cols-3 gap-8 md:gap-12">
        <div class="md:col-span-2 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 self-start w-full">
            <x-ui.page-header tag="h1" title="{{ __('Maintenance & Repairs') }}" />

            <p class="mb-8 text-gray-600 dark:text-neutral-400">
                {{ __('Is your 3D printer not performing as it should? We offer maintenance and repair services to get you back to printing in no time. Please describe the issue in the form below.') }}
            </p>

            <div class="flex flex-col gap-4">
                <livewire:sytatsu.components.maintenance-repair-form service_type="maintenance" />
            </div>
        </div>

        <div class="md:col-span-1 flex flex-col gap-8">
            <x-ui.card-box title="{{ __('Our Expertise') }}" class="text-sm">
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('We have extensive experience with various 3D printer brands and models. From simple nozzle replacements to complex board repairs, we handle it all with care.') }}
                </p>
            </x-ui.card-box>

            <x-sytatsu.sidebar.help />
        </div>
    </div>
</div>

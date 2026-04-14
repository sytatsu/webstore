<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full flex flex-col gap-8">
    <div class="flex flex-col md:grid md:grid-cols-3 gap-8 md:gap-12">
        <div class="md:col-span-2 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 self-start w-full">
            <h1 class="text-2xl font-bold text-black dark:text-white mb-8 avenir-bold uppercase">
                {{ __('Maintenance & Repairs') }}
            </h1>

            <hr class="mb-8 border-gray-200 dark:border-gray-500">

            <p class="mb-8 text-gray-600 dark:text-neutral-400">
                {{ __('Is your 3D printer not performing as it should? We offer maintenance and repair services to get you back to printing in no time. Please describe the issue in the form below.') }}
            </p>

            <div class="flex flex-col gap-4">
                <livewire:sytatsu.components.maintenance-repair-form service_type="maintenance" />
            </div>
        </div>

        <div class="md:col-span-1 flex flex-col gap-8">
            <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-sm">
                <h3 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase text-sm">
                    {{ __('Our Expertise') }}
                </h3>

                <hr class="mb-4 border-gray-200 dark:border-gray-500">

                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('We have extensive experience with various 3D printer brands and models. From simple nozzle replacements to complex board repairs, we handle it all with care.') }}
                </p>
            </div>

            <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-sm">
                <h3 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase text-sm">
                    {{ __('Need Help?') }}
                </h3>

                <hr class="mb-4 border-gray-200 dark:border-gray-500">

                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('If you have any questions about our products, services or your order, please contact our support.') }}
                </p>

                <a href="{{ route('sytatsu.contact') }}" class="inline-flex items-center gap-x-2 text-primary hover:text-primary-dark font-bold avenir-bold transition-colors">
                    {{ __('Get in touch') }} <i class="fa fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

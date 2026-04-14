<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full px-4 md:px-6 lg:px-8 py-4 flex flex-col gap-8">
    <div class="flex flex-col md:grid md:grid-cols-3 gap-8 md:gap-12">
        <div class="md:col-span-2 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 self-start w-full">
            <h1 class="text-2xl font-bold text-black dark:text-white mb-8 avenir-bold uppercase">
                {{ __('Custom Print Request') }}
            </h1>

            <hr class="mb-8 border-gray-200 dark:border-gray-500">

            <p class="mb-8 text-gray-600 dark:text-neutral-400">
                {{ __('Have a specific design in mind? We can print it for you in high quality with various materials and colors. Please fill out the form below and we will get back to you with a quote.') }}
            </p>

            <livewire:sytatsu.components.print-request-form />
        </div>

        <div class="md:col-span-1 flex flex-col gap-8">
            <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-sm">
                <h3 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase text-sm">
                    {{ __('Why Custom Print?') }}
                </h3>

                <hr class="mb-4 border-gray-200 dark:border-gray-500">

                <div class="space-y-3 text-gray-600 dark:text-neutral-400">
                    <p>
                        {{ __('Custom 3D printing allows you to bring unique ideas to life. Whether it\'s a replacement part, a personalized gift, or a complex prototype, we have the tools to make it happen.') }}
                    </p>
                </div>
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

<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full flex flex-col gap-8">
    <div class="flex flex-col md:grid md:grid-cols-3 gap-8 md:gap-12">
        <div class="md:col-span-2 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 self-start w-full">
            <h1 class="text-2xl font-bold text-black dark:text-white mb-8 avenir-bold uppercase">
                {{ __('Get in touch with us') }}
            </h1>

            <hr class="mb-8 border-gray-200 dark:border-gray-500">

            <div class="space-y-4 mb-8">
                <p class="text-gray-600 dark:text-neutral-400">
                    {{ __('Have a question about our products, need a custom 3D print, or is your printer in need of some professional care? We\'re here to help!') }}
                </p>

                <p class="text-gray-600 dark:text-neutral-400">
                    {{ __('Fill out the form below, and we will get back to you as soon as possible. Whether it\'s a simple inquiry or a complex project, we value every message and look forward to connecting with you.') }}
                </p>
            </div>

            <livewire:sytatsu.components.contact-form />
        </div>

        <div class="md:col-span-1 flex flex-col gap-8">
            <x-sytatsu.sidebar.socials />
            <x-sytatsu.sidebar.services />

            <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-sm">
                <h3 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase text-sm">
                    {{ __('Contact information') }}
                </h3>

                <hr class="mb-4 border-gray-200 dark:border-gray-500">

                <div class="space-y-3 text-gray-600 dark:text-neutral-400">
                    <p class="flex items-center gap-x-2">
                        <i class="fa fa-envelope w-4"></i>
                        <a href="mailto:info@sytatsu.nl" class="underline">info@sytatsu.nl</a>
                    </p>
                    <p class="flex items-center gap-x-2">
                        <i class="fa fa-location-dot w-4"></i>
                        <span>Dordrecht</span>
                    </p>
                    <p class="flex items-center gap-x-2">
                        <i class="fa fa-building w-4"></i>
                        <span>kvk - 83166742</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

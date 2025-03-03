<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid max-w-[40rem] lg:max-w-full lg:grid-cols-7 lg:gap-x-8 xl:gap-x-12 lg:items-center">
        <div class="lg:col-span-3">
            <h1 class="block text-3xl font-bold text-gray-800 sm:text-4xl md:text-5xl lg:text-6xl dark:text-white sr-only">Sytatsu, Print & Shop</h1>

            <img src="{{ asset('/images/brands/brand.svg') }}" alt="brand" width="700" class="m-auto">
            <p class="mt-3 text-lg text-gray-800 dark:text-neutral-400 py-4 lg:py-0">
                {{ __('While we are working hard behind the scenes, we are already building beautiful things. Feel free to get in touch with us or check out our social media!') }}
            </p>

            <div class=" flex flex-col items-center gap-2 lg:flex-row sm:gap-3  ">
                <a class="lg:mr-4 lg:flex-grow w-full lg:w-auto py-3 px-4 mb-3 lg:mt-auto lg:mb-auto inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary-dark text-white hover:bg-primary focus:outline-none focus:bg-primary disabled:opacity-50 disabled:pointer-events-none"
                   href="{{ route('sytatsu.contact') }}">
                    {{ __('Get in touch') }} <i class="fa fa-paper-plane"></i>
                </a>
                <livewire:sytatsu.components.social-collection />
            </div>

        </div>

        <div class="lg:col-span-4 mt-10 lg:mt-0 hidden lg:block">
            <img class="w-full rounded-xl shadow-lg dark:shadow-primary" src="{{ asset('/images/sytatsu_hero.jpg') }}" width="900" height="450" alt="Hero Image">
        </div>
    </div>
</div>

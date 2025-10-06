<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid max-w-[40rem] lg:max-w-full lg:grid-cols-7 lg:gap-x-8 xl:gap-x-12 lg:items-center">
        <div class="lg:col-span-3">
            <h1 class="block text-3xl font-bold text-gray-800 sm:text-4xl md:text-5xl lg:text-6xl dark:text-white sr-only">Sytatsu, Print & Shop</h1>

            <img src="{{ Vite::asset('resources/images/brands/no_background_text_only.webp') }}" alt="brand" width="700" class="m-auto px-4 lg:px-4">
            <p class="mt-3 text-lg text-gray-800 dark:text-neutral-400 py-4 lg:py-0">
                {{ __('At Sytatsu, we’re working hard behind the scenes — but we’re already building beautiful things. Feel free to get in touch or follow Sytatsu on social media to see what we’re up to!') }}
            </p>

            <div class="flex flex-col items-center gap-2 lg:flex-row sm:gap-3  ">
                <x-ui.button.default.primary class="w-full" href="{{ route('sytatsu.contact') }}">
                    {{ __('Get in touch') }} <i class="fa fa-paper-plane"></i>
                </x-ui.button.default.primary>
                <livewire:sytatsu.components.social-collection />
            </div>

        </div>

        <div class="lg:col-span-4 mt-10 lg:mt-0 hidden lg:block">
            <img class="w-full rounded-xl shadow-lg dark:shadow-primary" src="{{ Vite::asset('resources/images/sytatsu_hero.jpg') }}" width="900" height="450" alt="Hero Image">
        </div>
    </div>
</div>

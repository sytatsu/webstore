<footer class="px-auto mt-auto bg-slate-50 dark:bg-slate-700 shadow-inner">
    <div class="container mt-auto max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 lg:pt-20 mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <div class="col-span-full lg:col-span-1">
                <a class="flex-none text-xl font-semibold text-white focus:outline-hidden focus:opacity-80" href="{{ route('sytatsu.welcome') }}" aria-label="Sytatsu">
                    <img src="{{ Vite::asset('resources/images/brands/no_background_text_only.webp') }}" alt="Sytatsu" width="150" class="ml-8 lg:ml-0">
                </a>
            </div>

            <div class="col-span-1">
                <h4 class="font-semibold dark:text-gray-100">
                    {{ __('About Sytatsu') }}
                </h4>

                <div class="mt-3 grid space-y-3">
                    <p>
                        <a class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 focus:outline-hidden focus:text-gray-200 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                           href="#">{{ __("General Terms & Conditions") }}</a></p>
                    <p>
                        <a class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 focus:outline-hidden focus:text-gray-200 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                           href="#">{{ __("Privacy statement") }}</a></p>
                    <p>
                        <a class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 focus:outline-hidden focus:text-gray-200 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                           href="{{ route('sytatsu.about') }}">{{ __("About us") }}</a></p>
                    <p>
                        <a class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 focus:outline-hidden focus:text-gray-200 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                           href="#">{{ __("Sitemap") }}</a></p>
                </div>
            </div>

            <div class="col-span-1">
                <h4 class="font-semibold dark:text-gray-100">
                    {{ __('Customer service') }}
                </h4>

                <div class="mt-3 grid space-y-3">
                    <p>
                        <a class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 focus:outline-hidden focus:text-gray-200 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                           href="{{ route('sytatsu.contact') }}">{{ __("Contact") }}</a></p>
                    <p>
                        <a class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 focus:outline-hidden focus:text-gray-200 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                           href="#">{{ __("Warranty & Complaints") }}</a></p>
                    <p>
                        <a class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 focus:outline-hidden focus:text-gray-200 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                           href="#">{{ __("Delivery time & shipping costs") }}</a>
                    <p>
                        <a class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 focus:outline-hidden focus:text-gray-200 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                           href="#">{{ __("Return conditions & registration") }}</a></p>
                </div>
            </div>
        </div>

        <div class="mt-5 sm:mt-12 gap-y-2 sm:gap-y-0 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center">
            <div class="flex flex-wrap justify-between items-center gap-2">
                <p class="text-sm text-gray-400 dark:text-neutral-400">
                    Copyright © {{ \Carbon\Carbon::now()->format('Y') }} - Sytatsu - {{ __('All rights reserved') }}
                </p>
            </div>

            <div>
                <a class="w-16 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-white hover:bg-white/10 focus:outline-hidden focus:bg-white/10 disabled:opacity-50 disabled:pointer-events-none" href="{{ config('socials.sytatsu.instagram.href') }}" target="_blank" >
                    <img class="p-3 block dark:hidden duration-300 hover:scale-110" src="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_Gradient_small.png') }}" alt="Instagram" />
                    <img class="p-3 hidden dark:block duration-300 hover:scale-110" src="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_White.svg') }}" alt="Instagram"/>
                </a>

                <a class="w-16 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-white hover:bg-white/10 focus:outline-hidden focus:bg-white/10 disabled:opacity-50 disabled:pointer-events-none" href="{{ config('socials.sytatsu.facebook.href') }}" target="_blank" >
                    <img class="p-3 block dark:hidden duration-300 hover:scale-110" src="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Primary.png') }}" alt="Facebook" />
                    <img class="p-3 hidden dark:block duration-300 hover:scale-110" src="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Secondary.png') }}" alt="Facebook"/>
                </a>
            </div>
        </div>
    </div>
</footer>

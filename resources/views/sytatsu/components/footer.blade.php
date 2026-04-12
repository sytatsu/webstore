<footer class="mt-auto bg-slate-50 dark:bg-slate-700 shadow-inner">
    <div class="mx-auto xl:min-w-[80rem] md:max-w-[85rem] w-full px-8 md:px-12 lg:px-16 py-10">
        <div class=" py-8 md:py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-18 sm:gap-12 md:gap-6 ">
                <div class="col-span-full md:col-span-1">
                    <a class="flex-none text-xl font-semibold text-white focus:outline-hidden focus:opacity-80" href="{{ route('sytatsu.webstore.welcome') }}" aria-label="Sytatsu">
                        <img src="{{ Vite::asset('resources/images/brands/no_background_text_only.webp') }}" alt="Sytatsu" width="150" class="ml-8 lg:ml-0">
                    </a>
                </div>

                <div class="col-span-1">
                    <h4 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase">
                        {{ __('About Sytatsu') }}
                    </h4>

                    <hr class="mb-4 border-gray-200 dark:border-gray-500">

                    <div class="mt-3 grid space-y-3">
                        <p>
                            <a class="inline-flex gap-x-2 text-gray-500 hover:text-gray-700 focus:outline-hidden focus:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                               href="{{ asset('files/General Terms and Conditions Sytatsu v1.pdf') }}" target="_blank">{{ __("General Terms & Conditions") }}</a></p>
                        <p>
                            <a class="inline-flex gap-x-2 text-gray-500 hover:text-gray-700 focus:outline-hidden focus:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                               href="{{ asset('files/Privacy Policy v1.pdf') }}" target="_blank">{{ __("Privacy statement") }}</a></p>
                        <p>
                            <a class="inline-flex gap-x-2 text-gray-500 hover:text-gray-700 focus:outline-hidden focus:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                               href="{{ route('sytatsu.about') }}">{{ __("About us") }}</a></p>
                        <p>
                            <a class="inline-flex gap-x-2 text-gray-500 hover:text-gray-700 focus:outline-hidden focus:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                               href="#">{{ __("Sitemap") }}</a></p>
                    </div>
                </div>

                <div class="col-span-1">
                    <h4 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase">
                        {{ __('Customer service') }}
                    </h4>

                    <hr class="mb-4 border-gray-200 dark:border-gray-500">

                    <div class="mt-3 grid space-y-3">
                        <p>
                            <a class="inline-flex gap-x-2 text-gray-500 hover:text-gray-700 focus:outline-hidden focus:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                               href="{{ route('sytatsu.contact') }}">{{ __("Contact") }}</a></p>
                        <p>
                            <a class="inline-flex gap-x-2 text-gray-500 hover:text-gray-700 focus:outline-hidden focus:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                               href="{{ asset('files/Warranty & Complaints v1.pdf') }}" target="_blank">{{ __("Warranty & Complaints") }}</a></p>
                        <p>
                            <a class="inline-flex gap-x-2 text-gray-500 hover:text-gray-700 focus:outline-hidden focus:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                               href="{{ asset('files/Delivery Time & Shipping Costs v1.pdf') }}" target="_blank">{{ __("Delivery time & shipping costs") }}</a>
                        <p>
                            <a class="inline-flex gap-x-2 text-gray-500 hover:text-gray-700 focus:outline-hidden focus:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                               href="{{ asset('files/Return Conditions & Registration v1.pdf') }}" target="_blank">{{ __("Return conditions & registration") }}</a></p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-500 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-y-4 sm:gap-y-0">
                <div class="flex flex-wrap justify-between items-center gap-2">
                    <p class="text-sm text-gray-500 dark:text-neutral-400">
                        Copyright © {{ \Carbon\Carbon::now()->format('Y') }} - Sytatsu - {{ __('All rights reserved') }}
                    </p>
                </div>

                <div class="flex gap-2">
                    <a class="w-12 h-12 inline-flex justify-center items-center rounded-lg border border-transparent duration-300 hover:scale-110" href="{{ config('socials.sytatsu.instagram.href') }}" target="_blank" >
                        <img class="p-2 block dark:hidden" src="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_Gradient_small.png') }}" alt="Instagram" />
                        <img class="p-2 hidden dark:block" src="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_White.svg') }}" alt="Instagram"/>
                    </a>

                    <a class="w-12 h-12 inline-flex justify-center items-center rounded-lg border border-transparent duration-300 hover:scale-110" href="{{ config('socials.sytatsu.facebook.href') }}" target="_blank" >
                        <img class="p-2 block dark:hidden" src="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Primary.png') }}" alt="Facebook" />
                        <img class="p-2 hidden dark:block" src="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Secondary.png') }}" alt="Facebook"/>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

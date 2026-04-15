<x-ui.card-box title="{{ __('Follow Us') }}" class="text-sm">
    <p class="mb-6 text-gray-600 dark:text-neutral-400">
        {{ __('Stay updated with our latest projects and creations by following us on social media.') }}
    </p>

    <div class="flex flex-row justify-center gap-6">
        @if (config('socials.sytatsu.instagram.enabled'))
            <a href="{{ config('socials.sytatsu.instagram.href') }}" target="_blank" class="group flex flex-col items-center gap-2 transition-transform duration-300 hover:scale-105">
                <div class="w-14 h-14 flex items-center justify-center rounded-2xl bg-slate-50 dark:bg-slate-700 shadow-sm group-hover:shadow-md transition-shadow">
                    <img class="w-8 h-8 block dark:hidden" src="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_Gradient_small.png') }}" alt="Instagram" />
                    <img class="w-8 h-8 hidden dark:block" src="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_White.svg') }}" alt="Instagram" />
                </div>
            </a>
        @endif

        @if (config('socials.sytatsu.facebook.enabled'))
            <a href="{{ config('socials.sytatsu.facebook.href') }}" target="_blank" class="group flex flex-col items-center gap-2 transition-transform duration-300 hover:scale-105">
                <div class="w-14 h-14 flex items-center justify-center rounded-2xl bg-slate-50 dark:bg-slate-700 shadow-sm group-hover:shadow-md transition-shadow">
                    <img class="w-8 h-8 block dark:hidden" src="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Primary.png') }}" alt="Facebook" />
                    <img class="w-8 h-8 hidden dark:block" src="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Secondary.png') }}" alt="Facebook" />
                </div>
            </a>
        @endif
    </div>
</x-ui.card-box>

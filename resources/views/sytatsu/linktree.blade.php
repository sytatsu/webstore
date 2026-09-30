<div class="flex flex-col grow items-center justify-center px-5 py-8 sm:py-12"
     style="padding-top: max(2rem, env(safe-area-inset-top)); padding-bottom: max(2rem, env(safe-area-inset-bottom));">
    <div class="w-full max-w-[24rem] sm:max-w-[26rem] flex flex-col items-center gap-6 sm:gap-8">
        <a href="{{ route('sytatsu.webstore.welcome') }}" aria-label="Sytatsu" class="focus:outline-hidden focus:opacity-80">
            <img src="{{ Vite::asset('resources/images/brands/no_background_text_only.webp') }}" alt="Sytatsu" class="w-44 sm:w-52 h-auto" />
        </a>

        <p class="text-center text-base sm:text-lg text-gray-800 dark:text-neutral-200">
            {{ __('Custom 3D prints & Clickerz Bars — find us here') }}
        </p>

        <div class="w-full flex flex-col gap-3 sm:gap-4">
            <x-ui.button.default.primary href="{{ route('sytatsu.webstore.welcome') }}" class="flex items-center justify-center gap-x-3 !py-4 !text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                    <path d="M3 6h18" />
                    <path d="M16 10a4 4 0 0 1-8 0" />
                </svg>
                {{ __('Visit our webshop') }}
            </x-ui.button.default.primary>

            <x-ui.button.default.secondary href="{{ route('sytatsu.webstore.clickerz-bar-builder') }}" class="flex items-center justify-center gap-x-3 !py-4 !text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3a9 9 0 1 0 9 9 3.5 3.5 0 0 1-3.5-3.5A3.5 3.5 0 0 1 21 5" />
                    <circle cx="7.5" cy="10.5" r="1" fill="currentColor" stroke="none" />
                    <circle cx="9.5" cy="15.5" r="1" fill="currentColor" stroke="none" />
                    <circle cx="14.5" cy="16.5" r="1" fill="currentColor" stroke="none" />
                </svg>
                {{ __('Build a Clickerz Bar') }}
            </x-ui.button.default.secondary>

            <x-ui.button.outline.neutral href="{{ config('socials.sytatsu.instagram.href') }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-x-3 !py-4 !text-sm">
                <img class="size-5 shrink-0 block dark:hidden" src="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_Gradient_small.png') }}" alt="" />
                <img class="size-5 shrink-0 hidden dark:block" src="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_White.svg') }}" alt="" />
                {{ __('Follow us on Instagram') }}
            </x-ui.button.outline.neutral>

            <x-ui.button.outline.neutral href="{{ config('socials.sytatsu.facebook.href') }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-x-3 !py-4 !text-sm">
                <img class="size-5 shrink-0 block dark:hidden" src="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Primary.png') }}" alt="" />
                <img class="size-5 shrink-0 hidden dark:block" src="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Secondary.png') }}" alt="" />
                {{ __('Follow us on Facebook') }}
            </x-ui.button.outline.neutral>
        </div>
    </div>
</div>

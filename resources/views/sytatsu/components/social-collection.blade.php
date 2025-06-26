<div class="flex flex-row justify-center gap-3 m-auto">
    @if (config('socials.sytatsu.instagram.enabled'))
        <livewire:sytatsu.components.social-tile
            config="socials.sytatsu.instagram"
            srcLight="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_Gradient_small.png') }}"
            srcDark="{{ Vite::asset('resources/images/partials/socials/Instagram_Glyph_White.svg') }}"
            alt="Instagram" />
    @endif

    @if (config('socials.sytatsu.facebook.enabled'))
        <livewire:sytatsu.components.social-tile
            config="socials.sytatsu.facebook"
            srcLight="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Primary.png') }}"
            srcDark="{{ Vite::asset('resources/images/partials/socials/Facebook_Logo_Secondary.png') }}"
            alt="Facebook" />
    @endif
</div>

<div class="flex flex-row justify-center gap-3 m-auto">
    @if (config('socials.sytatsu.instagram.enabled'))
        <livewire:sytatsu.components.social-tile
            config="socials.sytatsu.instagram"
            src="/images/partials/socials/Instagram_Glyph_White.svg"
            alt="Instagram" />
    @endif

    @if (config('socials.sytatsu.facebook.enabled'))
        <livewire:sytatsu.components.social-tile
            config="socials.sytatsu.facebook"
            src="{{ asset('/images/partials/socials/Facebook_Logo_Secondary.png') }}"
            alt="Facebook" />
    @endif

    @if (config('socials.sytatsu.x.enabled'))
        <livewire:sytatsu.components.social-tile
            config="socials.sytatsu.x"
            src="{{ asset('/images/partials/socials/x-logo-black.png') }}"
            alt="x" />
    @endif
</div>

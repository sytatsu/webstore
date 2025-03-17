<div class="flex flex-row justify-center gap-3 m-auto">
    @if (config('socials.sytatsu.instagram.enabled'))
        <livewire:sytatsu.components.social-tile
            config="socials.sytatsu.instagram"
            srcLight="/images/partials/socials/Instagram_Glyph_Gradient_small.png"
            srcDark="/images/partials/socials/Instagram_Glyph_White.svg"
            alt="Instagram" />
    @endif

    @if (config('socials.sytatsu.facebook.enabled'))
        <livewire:sytatsu.components.social-tile
            config="socials.sytatsu.facebook"
            srcLight="{{ asset('/images/partials/socials/Facebook_Logo_Primary.png') }}"
            srcDark="{{ asset('/images/partials/socials/Facebook_Logo_Secondary.png') }}"
            alt="Facebook" />
    @endif
</div>

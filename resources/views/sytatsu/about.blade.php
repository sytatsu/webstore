<div class="max-w-4xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
    <div class="space-y-5 md:space-y-8">
        <div class="space-y-3">
            <h2 class="text-2xl font-bold md:text-3xl dark:text-white">
                <i class="fa fa-person text-primary"></i><i class="fa fa-person-dress text-secondary"></i> {{ __('About Us') }}
            </h2>

            <div class="space-y-4">
                <p class="text-lg text-gray-800 dark:text-neutral-200">
                    {{ __('We are Angela and Steve, the team behind Sytatsu. What started as a shared hobby has grown into a real 3D printing business.') }}
                </p>

                <p class="text-lg text-gray-800 dark:text-neutral-200">
                    {{ __('Steve is the technical mastermind – he loves diving into design, printer settings, and fine-tuning every detail. Angela is the creative force behind our social media and enjoys connecting with our customers – something we both truly value.') }}
                </p>

                <p class="text-lg text-gray-800 dark:text-neutral-200">
                    {{ __('Our passion? Creating things that don’t exist yet. We noticed that many people had ideas or needed certain products, but just couldn’t find them anywhere. With Sytatsu, we bring those ideas to life.') }}
                </p>
            </div>
        </div>

        <div class="space-y-3">
            <h2 class="text-2xl font-bold md:text-3xl dark:text-white">
                <i class="fa fa-paint-brush text-secondary underline decoration-primary"></i> {{ __('What we make & offer') }}
            </h2>

            <div class="space-y-4">
                <p class="text-lg text-gray-800 dark:text-neutral-200">
                    {{ __('We work with multiple Bambu Lab multi-color printers, powerful machines that allow us to print in high quality and vibrant color.') }}
                </p>

                <div class="space-y-1">
                    <p class="text-lg text-gray-800 dark:text-neutral-200">{{ __('We use a variety of filaments, including:') }}</p>

                    <ul class="text-lg text-gray-800 dark:text-neutral-200 pl-3">
                        <li><i class="fa fa-minus pr-2"></i> {{ __('PLA – durable and versatile, perfect for toys and decorations') }}</li>
                        <li><i class="fa fa-minus pr-2"></i> {{ __('PETG – strong and slightly flexible, ideal for long-lasting prints') }}</li>
                        <li><i class="fa fa-minus pr-2"></i> {{ __('TPU – flexible material, great for special applications') }}</li>
                    </ul>
                </div>

                <div class="space-y-1">
                    <p class="text-lg text-gray-800 dark:text-neutral-200">{{ __('Our specialties include:') }}</p>

                    <ul class="text-lg text-gray-800 dark:text-neutral-200 pl-3">
                        <li><i class="fa fa-gamepad pr-2"></i> {{ __('Game-related items')  }}</li>
                        <li><i class="fa fa-dice pr-2"></i> {{ __('Toys and fantasy figures')  }}</li>
                        <li><i class="fa fa-masks-theater pr-2"></i> {{ __('Cosplay props and decorative pieces')  }}</li>
                    </ul>
                </div>

                <div class="space-y-1">
                    <p class="text-lg text-gray-800 dark:text-neutral-200">{{ __('What matters to us:') }}</p>

                    <ul class="text-lg text-gray-800 dark:text-neutral-200 pl-3">
                        <li><i class="fa fa-check pr-2"></i> {{ __('Durable products')  }}</li>
                        <li><i class="fa fa-check pr-2"></i> {{ __('Clean, high-quality finishes')  }}</li>
                        <li><i class="fa fa-check pr-2"></i> {{ __('Fair pricing')  }}</li>
                    </ul>
                <div>

                <div class="pt-4">
                    <p class="text-lg text-gray-800 dark:text-neutral-200">
                        {{ __('We offer Print on Demand, as well as custom design services; Got an idea? Write it down in detail, and we’ll turn it into reality.') }}
                    </p>
                </div>
            </div>
        </div>


        <div class=" flex flex-col items-center gap-2 md:flex-row sm:gap-3  ">
            {{-- @TODO: Make this section compatible with the current component. now not possible because of different sizes & flex --}}
            <a class="m:mr-4 md:flex-grow w-full md:w-auto py-3 px-4 mb-3 md:mt-auto md:mb-auto inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary text-white hover:bg-primary-light focus:outline-none focus:bg-primary-light disabled:opacity-50 disabled:pointer-events-none"
               href="{{ route('sytatsu.contact') }}">
                {{ __('Get in touch') }} <i class="fa fa-paper-plane"></i>
            </a>

            <div class="flex flex-row md:flex-grow justify-center md:justify-start gap-3 m-auto">
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
        </div>
    </div>
</div>

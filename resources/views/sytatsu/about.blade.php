<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full px-4 md:px-6 lg:px-8 py-4 flex flex-col gap-8">
    <div class="flex flex-col md:grid md:grid-cols-3 gap-8 md:gap-12">
        <div class="md:col-span-2 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 self-start w-full">
            <div class="space-y-5 md:space-y-8">
                <div class="space-y-3">
                    <h2 class="text-2xl font-bold text-black dark:text-white mb-8 avenir-bold uppercase">{{ __('About Us') }}</h2>

                    <hr class="mb-8 border-gray-200 dark:border-gray-500">

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
                    <h2 class="text-2xl font-bold text-black dark:text-white mb-8 avenir-bold uppercase">{{ __('What we make & offer') }}</h2>

                    <hr class="mb-8 border-gray-200 dark:border-gray-500">

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
                        </div>

                        <div class="pt-4">
                            <p class="text-lg text-gray-800 dark:text-neutral-200">
                                {{ __('We offer Print on Demand, as well as custom design services; Got an idea? Write it down in detail, and we’ll turn it into reality.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-1 flex flex-col gap-8">
            <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-sm">
                <h3 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase text-sm">
                    {{ __('Get in touch') }}
                </h3>

                <hr class="mb-4 border-gray-200 dark:border-gray-500">

                <p class="mb-6 text-gray-600 dark:text-neutral-400">
                    {{ __('Got an idea or a question? We\'d love to hear from you!') }}
                </p>

                <x-ui.button.default.primary class="w-full text-center" href="{{ route('sytatsu.contact') }}">
                    {{ __('Contact us') }} <i class="fa fa-paper-plane ml-2"></i>
                </x-ui.button.default.primary>

                <div class="flex flex-row justify-center gap-3 mt-8">
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

            <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-sm">
                <h3 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase text-sm">
                    {{ __('Need Help?') }}
                </h3>

                <hr class="mb-4 border-gray-200 dark:border-gray-500">

                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('If you have any questions about our products or your order, please contact our support.') }}
                </p>
            </div>
        </div>
    </div>
</div>

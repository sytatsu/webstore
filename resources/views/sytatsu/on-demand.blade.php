<div class="container flex min-h-dvh flex-col mx-auto py-20">

    <div class="absolute left-0 top-0 p-5">
        <a href="{{ route('sytatsu.welcome') }}" class="flex flex-row justify-center hover:underline">
            <img src="{{ asset('images/icons/chevron-right.svg') }}" alt="chevron-right" width="35px" class="rotate-180"><span class="my-auto">{{ __('common.back') }}</span>
        </a>
    </div>

    <div class="absolute top-0 right-0 p-5">
        <livewire:sytatsu.components.locale-switcher />
    </div>

    <div class="flex flex-col m-auto justify-center overflow-hidden w-full rounded-b-lg">
        <div class="flex justify-center px-0 lg:px-12 py-4 md:py-8">
            <div class="md:my-auto w-full lg:w-2/3 px-5 md:pl-8 text-lg">

                <img src="{{ asset('/images/brands/brand.svg') }}" alt="brand" width="450" class="m-auto px-6 sm:px-4 pb-12">

                <p class="py-1 lg:py-2">{!! __('pages/on-demand.paragraph-1', ['email' => '<a class="hover:underline text-primary font-bold" href="mailto:info@sytatsu">info@sytatsu.nl</a>']) !!}</p>

                <div class="py-1 lg:py-2">
                    <ul class="pl-5 list-disc">
                        <li>{{ __('pages/on-demand.list.0') }}</li>
                        <li>{{ __('pages/on-demand.list.1') }}</li>
                        <li>{{ __('pages/on-demand.list.2') }}</li>
                    </ul>
                    <ul class="pl-10 list-disc">
                        <li>0.2mm</li>
                        <li>0.4mm</li>
                        <li>0.6mm</li>
                    </ul>
                </div>

                <p class="py-1 lg:py-2">{{ __('pages/on-demand.social-plug') }}</p>
            </div>
        </div>

        <div class="flex py-4 md:py-8 mx-auto">
            <livewire:sytatsu.components.social-collection />
        </div>

        <p class="flex justify-center m-auto text-secondary-light">
            {!! __('partials/footer.creator', ['creator' => '<a class="ml-1 underline text-secondary font-bold" href="https://stpronk.nl/">stpronk.nl</a>']) !!}
        </p>
    </div>
</div>

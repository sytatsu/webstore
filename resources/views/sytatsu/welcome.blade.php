<div class="flex flex-col min-h-dvh justify-center content-center py-20">
    <div class="flex flex-row justify-center">
        <div class="absolute top-0 right-0 p-5">
            <livewire:sytatsu.components.locale-switcher />
        </div>



        <div class="flex flex-col gap-2 text-center justify-center m-auto md:pt-0">
            <img src="{{ asset('/images/brands/brand.svg') }}" alt="brand" width="600" class="m-auto px-6 sm:px-4 pb-5">

            <div class="pt-4">
                <p class="italic font-bold text-2xl px-8 sm:px-0 pb-5 text-slate-700">{{ __('pages/coming-soon.heading') }}</p>
                <p class="m-auto w-full px-8 sm:px-0 sm:w-2/4 pb-4 ">{!! __('pages/coming-soon.services', [
                    'on-demand' => '<a href="'. route('sytatsu.on-demand').'" class="underline text-primary-dark font-bold">"Print on Demand"</a>',
                ]) !!}</p>
                <p class="m-auto  w-full px-8 sm:px-0 sm:w-2/3">{{ __('pages/coming-soon.socials') }}</p>
            </div>

            <livewire:sytatsu.components.social-collection />

            <p class="flex justify-center m-auto text-secondary-light">
                {!! __('partials/footer.creator', ['creator' => '<a class="ml-1 underline text-secondary font-bold" href="https://stpronk.nl/">stpronk.nl</a>']) !!}
            </p>
        </div>
    </div>
</div>

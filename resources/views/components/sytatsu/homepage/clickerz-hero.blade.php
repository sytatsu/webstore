<!-- Clickerz Bar Builder Hero -->
<div class="relative mb-8 rounded-2xl overflow-hidden">
    <div class="relative rounded-2xl bg-linear-to-br from-primary to-primary-dark dark:from-slate-900 dark:to-black shadow-md dark:shadow-slate-700">
        <div class="relative z-20 px-6 pt-12 pb-12 md:px-12 md:py-20 flex flex-col md:flex-row items-center justify-between gap-10 md:gap-12 min-h-[350px] md:min-h-[420px]">
            <div class="flex flex-col gap-6 w-full md:max-w-none md:w-3/5">
                <div class="inline-flex items-center gap-2 w-fit px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white/90 text-xs font-bold uppercase tracking-wide avenir-bold">
                    <i class="fa fa-star fa-sm"></i> <span class="mt-1">{{ __('New') }}</span>
                </div>

                <div class="max-w-xl text-left">
                    <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-4 md:mb-6 avenir-bold tracking-tight">
                        {{ __('Build your own Clickerz Bar.') }}
                    </h1>
                    <p class="text-base md:text-xl text-white/90 mb-0 font-medium">
                        {{ __('Pick your colours, add your icons, spell out your own word. Design a fully custom clicker bar and make it yours.') }}
                    </p>
                </div>

                <div class="flex flex-wrap justify-start gap-4">
                    <a href="{{ route('sytatsu.webstore.clickerz-bar-builder') }}" class="px-6 py-2.5 md:px-8 md:py-3 bg-white dark:bg-primary-dark text-primary dark:text-white avenir-bold hover:bg-gray-100 dark:hover:bg-primary font-bold rounded-xl transition-colors shadow-lg text-sm md:text-base">
                        {{ __('Start Building') }}
                    </a>
                </div>
            </div>

            <!-- Mobile: single contained example -->
            <div class="relative md:hidden w-4/5 max-w-[280px] flex items-center justify-center animate-fade-in-right" style="animation-delay: 200ms;">
                <div class="absolute inset-0 bg-white/60 dark:bg-white/20 rounded-full blur-3xl scale-125"></div>
                <img src="{{ Vite::asset('resources/images/seeders/clickerz-bar-hero.svg') }}"
                     alt="{{ __('Clickerz Bar Builder') }}"
                     class="relative z-10 w-full drop-shadow-2xl -rotate-3"
                >
            </div>

            <!-- Hero Image Container (Spacer for relative positioning) -->
            <div class="relative hidden md:block md:w-1/3 lg:w-2/5">
            </div>
        </div>

        <!-- Desktop: fanned, overlapping examples bleeding off the edge -->
        <div class="hidden md:block absolute top-1/2 md:-right-24 -translate-y-1/2 z-30 w-[380px] lg:w-[460px]">
            <div class="absolute inset-0 bg-linear-to-br bg-white/60 dark:bg-white/20 rounded-full blur-3xl h-[110%] lg:h-[135%] aspect-square"></div>

            <div class="relative flex flex-col items-end">
                <img src="{{ Vite::asset('resources/images/seeders/clickerz-bar-hero-wow.svg') }}"
                     alt="{{ __('Clickerz Bar Builder') }}"
                     class="relative z-10 w-3/5 -mb-8 rotate-[18deg] drop-shadow-2xl animate-fade-in-right"
                     style="animation-delay: 150ms;"
                >
                <img src="{{ Vite::asset('resources/images/seeders/clickerz-bar-hero-game.svg') }}"
                     alt="{{ __('Clickerz Bar Builder') }}"
                     class="relative z-20 w-4/5 -mb-6 -rotate-[10deg] drop-shadow-2xl animate-fade-in-right"
                     style="animation-delay: 300ms;"
                >
                <img src="{{ Vite::asset('resources/images/seeders/clickerz-bar-hero.svg') }}"
                     alt="{{ __('Clickerz Bar Builder') }}"
                     class="relative z-30 w-full rotate-[6deg] scale-150 drop-shadow-2xl hover:rotate-0 translate-y-1/2 transition-transform duration-500 animate-fade-in-right"
                     style="animation-delay: 450ms;"
                >
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-64 h-64 bg-white/10 rounded-full blur-3xl z-10"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl z-10"></div>
    </div>
</div>

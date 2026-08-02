<!-- Hero Section -->
<div class="relative mb-8 rounded-2xl overflow-hidden">
    <div class="relative rounded-2xl bg-linear-to-br from-primary to-primary-dark dark:from-slate-800 dark:to-slate-900 shadow-md dark:shadow-slate-700 ">
        <div class="relative z-20 px-6 pt-12 pb-12 md:px-12 md:py-20 flex flex-col md:flex-row items-center justify-between gap-8 md:gap-12 min-h-[400px] md:min-h-[500px]">
            <div class="flex flex-col gap-8 w-full md:max-w-none">
                <div class="flex flex-row items-center justify-between gap-4 md:block">
                    <div class="max-w-[60%] md:max-w-xs lg:max-w-md xl:max-w-2xl text-left">
                        <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-4 md:mb-6 avenir-bold tracking-tight">
                            {{ __('At Sytatsu, we bring your ideas to life.') }}
                        </h1>
                        <p class="text-base md:text-xl text-white/90 mb-0 md:mb-8 font-medium">
                            {{ __('From custom 3D designs to high-quality multi-color prints. We specialize in game-related items, toys, and unique decorative pieces.') }}
                        </p>
                    </div>

                    <div class="md:hidden absolute top-0 right-0 translate-x-3/5 z-30 aspect-square animate-fade-in-right" style="animation-delay: 200ms;">
                        <div class="relative w-full h-full group">
                            <!-- Decorative background for the image -->
                            <div class="absolute inset-0 bg-white/60 dark:bg-white/20 rounded-full blur-2xl group-hover:bg-white/80 dark:group-hover:bg-white/30 transition-colors duration-500 scale-110"></div>

                            <!-- The Cut-out Image -->
                            <img src="{{ Vite::asset('resources/images/polymaker/panchroma_matte_pla_sky_blue.webp') }}"
                                 alt="Sytatsu Hero"
                                 class="relative z-10 w-full h-full object-cover drop-shadow-2xl aspect-square overflow-hidden"
                            >
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap justify-start gap-4">
                    <a href="{{ route('sytatsu.webstore.collections') }}" class="px-6 py-2.5 md:px-8 md:py-3 bg-white dark:bg-primary-dark text-primary dark:text-white avenir-bold hover:bg-gray-100 dark:hover:bg-primary font-bold rounded-xl transition-colors shadow-lg text-sm md:text-base">
                        {{ __('View Collections') }}
                    </a>
                    <a href="{{ route('sytatsu.about') }}" class="px-6 py-2.5 md:px-8 md:py-3 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-colors backdrop-blur-sm border border-white/20 text-sm md:text-base">
                        {{ __('About Us') }}
                    </a>
                </div>
            </div>

            <!-- Hero Image Container (Spacer for relative positioning) -->
            <div class="relative hidden md:block md:w-1/3 lg:w-1/2 md:aspect-auto">
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-64 h-64 bg-white/20 rounded-full blur-3xl z-10"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl z-10"></div>
    </div>

    <!-- Absolutely Positioned Hero Image -->
    <div class="hidden md:block absolute top-1/2 right-0 -translate-y-1/2 translate-x-1/2 lg:translate-x-2/5 xl:translate-x-1/3 z-30 h-[110%] lg:h-[135%] aspect-square animate-fade-in-right" style="animation-delay: 200ms;">
        <div class="relative w-full h-full group">
            <!-- Decorative background for the image -->
            <div class="absolute inset-0 bg-white/60 dark:bg-white/20 rounded-full blur-2xl group-hover:bg-white/80 dark:group-hover:bg-white/30 transition-colors duration-500 scale-110"></div>

            <!-- The Cut-out Image -->
            <img src="{{ Vite::asset('resources/images/polymaker/panchroma_matte_pla_sky_blue.webp') }}"
                 alt="Sytatsu Hero"
                 class="relative z-10 w-full h-full object-cover drop-shadow-2xl aspect-square overflow-hidden"
            >
        </div>
    </div>
</div>

<div data-hs-carousel='{
    "loadingClasses": "opacity-0",
    "dotsItemClasses": "hs-carousel-active:bg-primary hs-carousel-active:border-white size-3 border border-neutral-200 rounded-2xl cursor-pointer dark:border-neutral-200 dark:hs-carousel-active:bg-primary dark:hs-carousel-active:border-white",
    "isInfiniteLoop": true,
    "isDraggable": true
}' class="relative">
    <div class="hs-carousel relative overflow-x-hidden min-w-full bg-white mb-4 md:mb-0 shadow-lg dark:shadow-slate-700">

        @if ($this->carouselType === \App\Enums\CarouselTypeEnum::COMPACT)
            <a class="flex flex-col aspect-square bg-gray-100 dark:bg-slate-700" href="{{ \App\Services\WebstoreHelperService::getProductRoute($this->product) }}">
                <div class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap transition-transform duration-700 min-w-full overflow-hidden opacity-0 hs-carousel-initialized:opacity-100">
                    @forelse($this->images as $image)
                        <div class="hs-carousel-slide overflow-hidden">
                            <img class="size-full object-cover aspect-square transform transition-all scale-101 hover:scale-105"
                                 src="{{ $image->getUrl('medium') }}"
                                 alt="{{ $image->name }}">
                        </div>
                    @empty
                        <div class="hs-carousel-slide">
                            <div class="w-full h-full flex flex-col gap-2 items-center justify-center text-gray-400">
                                <i class="fa fa-image text-4xl"></i>
                                <span>{{ __('Image coming soon') }}</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </a>
        @else {{-- \App\Enums\CarouselTypeEnum::EXPANDED --}}
            <div class="flex flex-col aspect-square bg-gray-100 dark:bg-slate-700">
                <div class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap transition-transform duration-700 min-w-full overflow-hidden opacity-0 hs-carousel-initialized:opacity-100">
                    @forelse($this->images as $image)
                        <div class="hs-carousel-slide overflow-hidden">
                            <img class="size-full object-cover aspect-square transform transition-all scale-101 hover:scale-105"
                                 src="{{ $image->getUrl('large') }}"
                                 alt="{{ $image->name }}">
                        </div>
                    @empty
                        <div class="hs-carousel-slide">
                            <div class="w-full h-full flex flex-col gap-2 items-center justify-center text-gray-400">
                                <i class="fa fa-image text-4xl"></i>
                                <span>{{ __('Image coming soon') }}</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        @if ($this->images->count() > 1)
            <button type="button" class="hs-carousel-prev hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute px-2 inset-y-0 start-0 inline-flex justify-center items-center w-11.5 h-full focus:outline-hidden focus:bg-gray-800/10 rounded-s-lg text-black hover:bg-white/10 focus:bg-white/10">
                <span class="text-2xl" aria-hidden="true">
                  <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                       fill="none" stroke="currentColor"
                       stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"></path>
                    <path d="m15 18-6-6 6-6"></path>
                  </svg>
                </span>
                <span class="sr-only">{{ __('Previous') }}</span>
            </button>
            <button type="button" class="hs-carousel-next hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute px-2 inset-y-0 end-0 inline-flex justify-center items-center w-11.5 h-full focus:outline-hidden focus:bg-gray-800/10 rounded-e-lg text-black hover:bg-white/10 focus:bg-white/10">
                <span class="sr-only">{{ __('Next') }}</span>
                <span class="text-2xl" aria-hidden="true">
                  <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                       fill="none" stroke="currentColor"
                       stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"></path>
                    <path d="m9 18 6-6-6-6"></path>
                  </svg>
                </span>
            </button>
        @endif
    </div>

    @if ($this->images->count() > 1)
        @if ($this->carouselType === \App\Enums\CarouselTypeEnum::COMPACT)
            <div class="hs-carousel-pagination flex justify-center absolute bottom-3 start-0 end-0 gap-x-2"></div>
        @else {{-- \App\Enums\CarouselTypeEnum::EXPANDED --}}
            <div class="hs-carousel-pagination overflow-x-auto">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-4">
                    @forelse($this->images as $image)
                        <div class="hs-carousel-pagination-item cursor-pointer hs-carousel-active:inset-ring-3 hs-carousel-active:inset-ring-secondary-light overflow-hidden rounded-lg">
                            <img class="object-cover aspect-video rounded-lg hs-carousel-active:p-1 scale-101"
                                 src="{{ $image->getUrl('small') }}"
                                 alt="{{ $image->name }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>

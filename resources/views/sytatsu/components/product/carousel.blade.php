<div data-hs-carousel='{
    "loadingClasses": "opacity-0",
    "dotsItemClasses": "hs-carousel-active:bg-primary hs-carousel-active:border-white size-3 border border-neutral-200 rounded-2xl cursor-pointer dark:border-neutral-200 dark:hs-carousel-active:bg-primary dark:hs-carousel-active:border-white",
    {{-- @TODO; isDraggable feels a bit buggy, see if we can fix this --}}
    "isDraggable": true
}' class="relative">
    <div class="hs-carousel relative overflow-hidden w-full aspect-square bg-white rounded-lg">
        <a class="flex flex-col hover:underline" href="{{ \App\DataTransformers\RouteTransformer::getProductRoute($this->product) }}">
            <div
                class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap transition-transform duration-700 opacity-0">
                @forelse($this->images as $image)
                    <div class="hs-carousel-slide">
                        <img class="size-full object-cover aspect-square transform transition-all scale-100 hover:scale-105"
                             src="{{ $image->getFullUrl() }}"
                             alt="{{ $image->name }}">
                    </div>
                @empty
                    {{-- @TODO; Placeholder image should be replaced, now image from outdated logo --}}
                    <div class="hs-carousel-slide">
                        <img class="size-full object-cover aspect-square transform transition-all scale-100 hover:scale-105"
                             src="{{ Vite::asset('resources/images/product_placeholder.jpg') }}"
                             alt="product_placeholder">
                    </div>
                @endforelse
            </div>
        </a>

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
                <span class="sr-only">Previous</span>
            </button>
            <button type="button" class="hs-carousel-next hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute px-2 inset-y-0 end-0 inline-flex justify-center items-center w-11.5 h-full focus:outline-hidden focus:bg-gray-800/10 rounded-e-lg text-black hover:bg-white/10 focus:bg-white/10">
                <span class="sr-only">Next</span>
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
        <div class="hs-carousel-pagination flex justify-center absolute bottom-3 start-0 end-0 flex gap-x-2"></div>
    @endif
</div>

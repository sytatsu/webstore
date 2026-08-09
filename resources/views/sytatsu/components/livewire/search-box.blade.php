<div class="my-auto" x-data="{ open: false }" @keydown.escape.window="open = false" x-effect="document.body.classList.toggle('overflow-hidden', open)">
    <button type="button"
            @click="open = true; $nextTick(() => $refs.searchBoxInput.focus())"
            class="flex font-medium text-gray-800 hover:text-gray-900 rounded-full focus:outline-none dark:text-neutral-200 dark:hover:text-neutral-300"
    >
        <span class="group inline-flex shrink-0 justify-center items-center size-9 my-auto">
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.3-4.3"/>
            </svg>
        </span>
    </button>

    <div x-show="open"
         x-transition:enter="transition duration-200 ease-out"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150 ease-in"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="open = false"
         class="fixed top-0 start-0 size-full z-80 bg-slate-900/20 backdrop-blur-sm flex flex-col md:items-center md:justify-center md:p-4"
         role="dialog" tabindex="-1" aria-modal="true" aria-label="{{ __('Search products...') }}"
         x-cloak
    >
        <div class="w-full h-full md:h-auto md:max-w-lg bg-white dark:bg-slate-800 md:rounded-2xl shadow-lg dark:shadow-slate-900 flex flex-col overflow-hidden"
             x-show="open"
             x-transition:enter="transition duration-200 ease-out"
             x-transition:enter-start="opacity-0 md:scale-90"
             x-transition:enter-end="opacity-100 md:scale-100"
             x-transition:leave="transition duration-150 ease-in"
             x-transition:leave-start="opacity-100 md:scale-100"
             x-transition:leave-end="opacity-0 md:scale-90"
        >
            <form wire:submit.prevent="search" class="flex items-center gap-2 shrink-0 px-4 py-3 border-b border-gray-100 dark:border-slate-700">
                <span class="shrink-0 text-gray-400 dark:text-neutral-500">
                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </span>

                <input type="search"
                       x-ref="searchBoxInput"
                       wire:model.live.debounce.300ms="query"
                       placeholder="{{ __('Search products...') }}"
                       autocomplete="off"
                       class="w-full py-1 bg-transparent border-0 focus:outline-none focus:ring-0 text-base text-gray-800 dark:text-neutral-200 placeholder:text-gray-400 dark:placeholder:text-neutral-500"
                />

                <button type="button" @click="open = false; $wire.query = ''"
                        class="shrink-0 size-8 inline-flex justify-center items-center rounded-full border border-transparent text-gray-800 hover:bg-primary hover:text-white focus:outline-hidden dark:text-neutral-400 dark:hover:text-white cursor-pointer transition"
                        aria-label="{{ __('Close') }}"
                >
                    <span class="sr-only">{{ __('Close') }}</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </form>

            <div class="flex-1 overflow-y-auto">
                @if(mb_strlen(trim($query)) < 2)
                    <p class="px-4 py-8 text-sm text-center text-gray-500 dark:text-gray-400 avenir-bold uppercase">
                        {{ __('Enter a search term to find products') }}
                    </p>
                @elseif($pages->isEmpty() && $results->isEmpty())
                    <p class="px-4 py-8 text-sm text-center text-gray-600 dark:text-gray-400 avenir-bold uppercase">
                        {{ __('No products found') }}
                    </p>
                @else
                    @foreach($pages as $page)
                        <a href="{{ $page['url'] }}"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 dark:hover:bg-slate-700 border-b border-gray-100 dark:border-slate-700"
                        >
                            <span class="shrink-0 size-10 flex items-center justify-center rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-neutral-400">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/>
                                </svg>
                            </span>
                            <span class="text-sm avenir-bold uppercase text-black dark:text-white">{{ $page['title'] }}</span>
                        </a>
                    @endforeach

                    @foreach($results as $product)
                        <a href="{{ \App\Services\WebstoreHelperService::getProductRoute($product) }}"
                           wire:key="search-result-{{ $product->id }}"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 dark:hover:bg-slate-700 border-b border-gray-100 dark:border-slate-700 last:border-b-0"
                        >
                            <img src="{{ $product->thumbnail?->getUrl('small') ?? \App\Services\WebstoreHelperService::productPlaceholderImage() }}"
                                 alt="{{ $product->translateAttribute('name') }}"
                                 class="w-10 h-10 object-cover rounded shrink-0"
                            />
                            <div class="flex flex-col overflow-hidden">
                                <span class="text-sm avenir-bold uppercase text-black dark:text-white truncate">{{ $product->translateAttribute('name') }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Services\WebstoreHelperService::priceRangeString($product->prices) }}</span>
                            </div>
                        </a>
                    @endforeach

                    @if($results->isNotEmpty())
                        <a href="{{ route('sytatsu.webstore.search', ['q' => $query]) }}"
                           class="block text-center px-4 py-3 font-mono text-[10px] tracking-[.16em] uppercase text-secondary hover:underline"
                        >
                            {{ __('See all results') }}
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

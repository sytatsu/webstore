<div class="{{ $maxWidth ?? 'max-w-[85rem]' }} w-full mx-auto">
    <div class="flex flex-col gap-8">
        <h1 class="text-2xl avenir-bold text-black dark:text-white uppercase">
            @if($query !== '')
                {{ __('Search results for') }} "{{ $query }}"
            @else
                {{ __('Search') }}
            @endif
        </h1>

        <div class="relative">
            <x-ui.spinner-overlay wire:loading.flex />

            @if($query === '')
                <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-center">
                    <p class="text-gray-600 dark:text-gray-400 avenir-bold uppercase">
                        {{ __('Enter a search term to find products') }}
                    </p>
                </div>
            @elseif($products->isEmpty() && $pages->isEmpty())
                <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-center">
                    <p class="text-gray-600 dark:text-gray-400 avenir-bold uppercase">
                        {{ __('No products found') }}
                    </p>
                </div>
            @else
                <div class="flex flex-col gap-8">
                    @if($pages->isNotEmpty())
                        <div class="flex flex-col gap-3">
                            @foreach($pages as $page)
                                <a href="{{ $page['url'] }}"
                                   class="flex items-center gap-3 rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-4 hover:bg-gray-50 dark:hover:bg-slate-700 transition"
                                >
                                    <span class="shrink-0 size-10 flex items-center justify-center rounded-full bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-neutral-400">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/>
                                        </svg>
                                    </span>
                                    <span class="text-sm avenir-bold uppercase text-black dark:text-white">{{ $page['title'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($products->isNotEmpty())
                        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-8 px-6 lg:p-12">
                            <div class="divide-y divide-gray-200 dark:divide-gray-500">
                                @if($pages->isNotEmpty())
                                    <div class="flex flex-row justify-between items-center pb-8">
                                        <span class="text-2xl avenir-bold text-black dark:text-white">
                                            {{ __('Products') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="{{ $pages->isNotEmpty() ? 'pt-8' : '' }} grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-6 md:gap-6 lg:gap-8 xl:gap-12">
                                    @foreach($products as $product)
                                        <livewire:sytatsu.components.product.product-tile :product="$product" :wire:key="'product-'.$product->id.'-'.md5($product->updated_at)" />
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<div class="flex flex-col gap-8">
    @forelse ($this->activeCollections as $collection)
        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-8 px-6 lg:p-12">
            <div class="divide-y divide-gray-200 dark:divide-gray-500">
                <div class="group flex flex-row justify-between items-center pb-8">
                    @if($showMore)
                        <a class="text-2xl avenir-bold text-black dark:text-white hover:underline uppercase" href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->collection) }}">
                            {{ $collection->getName() }}
                        </a>
                        <a class="font-mono text-[10px] tracking-[.16em] uppercase text-secondary" href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->collection) }}">
                            <span>{{ __('Show more') }}</span>
                            <i class="fa fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    @else
                        <span class="text-2xl avenir-bold text-black dark:text-white">
                            {{ $collection->getName() }}
                        </span>
                    @endif
                </div>

                <div class="pt-8 grid {{ $gridColumns }} gap-x-4 gap-y-6 md:gap-6 lg:gap-8 xl:gap-12">
                    @foreach($collection->products as $product)
                        <livewire:sytatsu.components.product.product-tile :product="$product" :wire:key="'product-'.$product->id.'-'.md5($product->updated_at)" />
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-center">
            <p class="text-gray-600 dark:text-gray-400 avenir-bold uppercase">
                {{ __('No products found') }}
            </p>
        </div>
    @endforelse
</div>

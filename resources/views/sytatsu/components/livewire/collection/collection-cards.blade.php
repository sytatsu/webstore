<div class="flex flex-col gap-8">
    @forelse ($this->activeCollections as $collection)
        <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-8 px-6 lg:p-12">
            <div class="group flex flex-row justify-between items-center mb-8 ">
                @if($showMore)
                    <a class="text-2xl avenir-bold text-black dark:text-white hover:underline uppercase" href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->collection) }}">
                        {{ $collection->getName() }}
                    </a>
                    <a class="avenir-bold text-secondary uppercase text-sm tracking-widest" href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->collection) }}">
                        <span>{{ __('Show more') }}</span>
                        <i class="fa fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                    </a>
                @else
                    <span class="text-2xl avenir-bold text-black dark:text-white">
                        {{ $collection->getName() }}
                    </span>
                @endif
            </div>

            <hr class="mb-8 border-gray-200 dark:border-gray-500">

            <div class="grid {{ $gridColumns }} gap-x-4 gap-y-6 md:gap-6 lg:gap-8 xl:gap-12">
                <x-ui.spinner-overlay wire:loading.flex />
                @foreach($collection->products as $product)
                    <livewire:sytatsu.components.product.product-tile :product="$product" :wire:key="'product-'.$product->id.'-'.md5($product->updated_at)" />
                @endforeach
            </div>
        </div>
    @empty
        <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-center">
            <p class="text-gray-600 dark:text-gray-400 avenir-bold uppercase">
                {{ __('No products found') }}
            </p>
        </div>
    @endforelse
</div>

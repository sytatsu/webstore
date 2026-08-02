<div class="{{ $maxWidth ?? 'max-w-[85rem]' }} w-full mx-auto">
    <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-8 px-6 lg:p-12">
        <div class="divide-y divide-gray-200 dark:divide-gray-500">
            <div class="group flex flex-row justify-between items-center pb-8">
                <h1 class="text-2xl avenir-bold text-black dark:text-white uppercase">
                    {{ __('All Collections') }}
                </h1>
            </div>

            <div class="pt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-6 md:gap-6 lg:gap-8 xl:gap-12">
                @forelse($collections as $collection)
                    <a class="group flex flex-col gap-2 md:gap-4 h-full" href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection) }}">
                        <div class="relative overflow-hidden rounded-2xl bg-gray-100 dark:bg-slate-700 shadow-lg dark:shadow-slate-700 aspect-square">
                            <div class="block w-full h-full">
                                @php
                                    $collectionImage = $collection->attribute_data->get('collection_image')?->getValue();
                                @endphp
                                @if($collectionImage)
                                    <img src="{{ asset('storage/' . $collectionImage) }}" alt="{{ $collection->translateAttribute('name') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @elseif($collection->thumbnail)
                                    <img src="{{ $collection->thumbnail->getUrl('medium') }}" alt="{{ $collection->translateAttribute('name') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <i class="fa fa-layer-group text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="group/link mt-2">
                            <h3 class="font-bold text-black dark:text-white avenir-bold uppercase tracking-widest group-hover/link:underline">
                                {{ $collection->translateAttribute('name') }}
                            </h3>
                        </div>

                        <div class="flex items-center font-mono text-[10px] tracking-[.16em] uppercase text-primary">
                            <span>{{ __('View collection') }}</span>
                            <i class="fa fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full p-8 md:p-12 text-center">
                        <p class="text-gray-600 dark:text-gray-400 avenir-bold uppercase">
                            {{ __('No collections found') }}
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

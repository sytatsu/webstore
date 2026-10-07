{{-- `group` ties the picture, name/price and the add-to-(bundle/cart)
     button below it together as one unit — hovering any of them
     highlights the whole card (background + button ring), rather than
     leaving the button looking unrelated to the image above it.

     For a bundle-eligible tile, the whole card is also one big "add
     to bundle" click target, not just the explicit button at the
     bottom — clicking anywhere that isn't already its own link/button
     (the title, the add/stepper controls themselves) forwards a click
     to whichever of those two states add-to-cart.blade.php is
     currently showing (`x-ref="bundleAddTrigger"` there), rather than
     duplicating addToBundle()'s call here. The title stays a plain
     link to the product's own detail page either way.

     `$el.querySelector(...)`, not Alpine's `$refs` magic — the add-to-cart
     control is a *separate* Livewire component, and every Livewire v3
     component root is itself an implicit Alpine scope, so an `x-ref`
     inside it is invisible to `$refs` from this outer scope (confirmed
     empirically: `$refs.bundleAddTrigger` came back undefined even
     though the element exists and is a plain DOM descendant). A plain
     DOM query isn't affected by that Alpine-scope boundary. --}}
{{-- `relative` here is what lets add-to-cart.blade.php's full-tile
     loading overlay (shown while addToBundle()/removeFromBundleOne()
     is in flight) anchor to the *whole tile* instead of just its own
     small corner of it — see the `coverTile` prop passed below and the
     note on that overlay for why it has to work this way. --}}
<div class="group relative flex flex-col gap-2 md:gap-4 p-3 -m-3 rounded-2xl transition-all duration-200 hover:bg-gray-50 hover:shadow-lg dark:hover:bg-slate-700/60 dark:hover:shadow-slate-900/40 @if($this->activeBundle) cursor-pointer @endif"
     @if($this->activeBundle)
         x-data="{}"
         @click="if (!$event.target.closest('a, button, input')) { $el.querySelector('[x-ref=bundleAddTrigger]')?.click() }"
     @endif
>
    <div class="relative" wire:key="product-carousel-{{ $this->product->id }}">
        <livewire:sytatsu.components.product.carousel :product="$this->product" :images="$this->product->images" :link-to-product="!$this->activeBundle" :wire:key="'carousel-'.$this->product->id" />

        <a class="md:mt-4 flex flex-col gap-2" href="{{ \App\Services\WebstoreHelperService::getProductRoute($this->product) }}">
            <div class="[&>*]:hover:underline">
                <h3 class="text-sm font-bold text-black dark:text-white avenir-bold uppercase tracking-widest">
                    {{ $this->product->translateAttribute('name') }}
                </h3>

                @unless ($this->activeBundle)
                    <p class=" text-black dark:text-white avenir-bold uppercase">
                        {{ $this->getPriceRangeString() }}
                    </p>
                @endunless
            </div>
        </a>
    </div>

{{--    <div class="mb-2 mt-4 text-sm">--}}
{{--        <div class="flex flex-col">--}}
{{--            --}}{{-- TODO; Every line/item should be it's own component --}}
{{--            @foreach($this->getProductOptionsArray() as $optionCollectionName => $options)--}}
{{--                <div class="py-3 border-t border-gray-200 dark:border-neutral-700">--}}
{{--                    <div class="grid grid-cols-2 gap-2">--}}
{{--                        <div>--}}
{{--                            <span class="font-medium text-black dark:text-white">{{ $optionCollectionName }}:</span>--}}
{{--                        </div>--}}

{{--                        <div class="text-end text-black dark:text-white">--}}
{{--                            @foreach($options as $option)--}}
{{--                                <span >{{ $option['name'] }}</span>{{ !$loop->last ? ', ' : '' }}--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endforeach--}}

{{--            <div class="py-3 border-t border-gray-200 dark:border-neutral-700">--}}
{{--                <div class="grid grid-cols-2 gap-2">--}}
{{--                    <div>--}}
{{--                        <span class="font-medium text-black dark:text-white">{{ __('Collection') }}:</span>--}}
{{--                    </div>--}}

{{--                    <div class="text-end text-black dark:text-white">--}}
{{--                        @foreach($this->product->collections as $collection)--}}
{{--                            <div class="block">--}}
{{--                                @if ($collection->parent)--}}
{{--                                    <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->parent) }}" class="hover:underline text-nowrap">{{ $collection->parent->translateAttribute('name') }}</a><span><i class="px-1 fa fa-caret-right"></i></span>--}}
{{--                                @endif--}}

{{--                                <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection) }}" class="hover:underline text-nowrap">{{ $collection->translateAttribute('name') }}</a>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

    {{-- @TODO; Should be converted to a livewire component --}}
    <div class="flex mt-auto rounded-xl ring-primary/40 ring-0 group-hover:ring-2 transition-[box-shadow] duration-200">
        @if ($this->product->variants->count() >= 2)
            <x-ui.button.outline.primary class="w-full" href="{{ \App\Services\WebstoreHelperService::getProductRoute($this->product) }}">
                {{ $this->product->variants->count() }} {{ __('variants') }}
            </x-ui.button.outline.primary>
        @else
            <livewire:sytatsu.components.add-to-cart
                :minimalistic="true"
                :purchasable="$this->product->variant"
                :cover-tile="true"
                :wire:key="$this->product->variant->id"
            />
        @endif
    </div>
</div>

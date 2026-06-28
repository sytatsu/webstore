
<div class="group flex flex-col gap-2 md:gap-4 relative">
    <x-ui.spinner-overlay wire:loading.flex wire:target="increment, decrement" />
    <div class="relative cursor-pointer" wire:key="bundle-tile-carousel-{{ $this->product->id }}" wire:click="increment">
        <livewire:sytatsu.components.product.carousel :product="$this->product" :images="$this->product->images" :wire:key="'bundle-carousel-'.$this->product->id" :disableLink="true" />

        @if ($quantity > 0)
            <div class="absolute top-2 right-2 bg-primary rounded-full w-8 h-8 flex items-center justify-center shadow-md z-10">
                <span class="text-white text-sm font-bold avenir-bold">{{ $quantity }}</span>
            </div>
        @endif

        @if ($this->product->variants->count() > 1)
            <div class="absolute bottom-2 left-2 bg-white/90 dark:bg-slate-800/90 text-[10px] px-2 py-1 rounded avenir-bold uppercase tracking-wider text-gray-700 dark:text-gray-200 z-10">
                {{ $this->product->variants->count() }} {{ __('variants') }}
            </div>
        @endif
    </div>

    <div class="flex flex-col gap-1">
        <div
            class="cursor-pointer [&>*]:hover:underline"
            wire:click="increment"
        >
            <h3 class="text-sm font-bold text-black dark:text-white avenir-bold uppercase tracking-widest leading-tight">
                {{ $this->product->translateAttribute('name') }}
            </h3>
            <p class="text-sm text-black dark:text-white avenir-bold uppercase">
                {{ $this->getPriceString() }}
            </p>
        </div>

        <div class="flex mt-auto">
            @if($this->availableStock === 0)
                <div class="w-full text-center py-2 text-xs font-bold avenir-bold uppercase tracking-widest text-primary border-2 border-primary/20">
                    {{ __('Out of stock') }}
                </div>
            @elseif ($quantity === 0)
                <x-ui.button.outline.primary
                    wire:click="increment"
                    class="w-full !py-2 !px-2"
                >
                    {{ __('Add to bundle') }}
                </x-ui.button.outline.primary>
            @else
                <div class="flex items-center w-full border-2 border-primary">
                    <button
                        type="button"
                        wire:click="decrement"
                        class="px-4 py-2 text-primary dark:text-white hover:bg-primary hover:text-white transition-colors font-bold"
                    >
                        −
                    </button>
                    <span class="flex-1 text-center text-xs font-bold avenir-bold text-black dark:text-white uppercase tracking-widest">{{ $quantity }}</span>
                    <button
                        type="button"
                        wire:click="increment"
                        @disabled($this->availableStock !== null && $quantity >= $this->availableStock)
                        class="px-4 py-2 text-primary dark:text-white hover:bg-primary hover:text-white transition-colors font-bold disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-primary"
                    >
                        +
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

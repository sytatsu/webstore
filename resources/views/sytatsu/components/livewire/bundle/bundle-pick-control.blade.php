<div>
    @if($variant)
        <div class="flex items-center gap-1 bg-white/95 dark:bg-slate-900/95 rounded-full shadow border border-gray-200 dark:border-slate-600 px-1 py-0.5">
            @if($quantity > 0)
                <button type="button"
                        wire:click.prevent="decrement"
                        class="size-6 inline-flex items-center justify-center rounded-full text-xs font-bold text-black dark:text-white hover:bg-gray-100 dark:hover:bg-slate-800"
                >
                    <i class="fa fa-minus"></i>
                </button>

                <span class="w-5 text-center text-xs avenir-bold text-black dark:text-white">{{ $quantity }}</span>
            @endif

            <button type="button"
                    wire:click.prevent="increment"
                    {{ $quantity >= $available ? 'disabled' : '' }}
                    class="size-6 inline-flex items-center justify-center rounded-full text-xs font-bold text-white bg-primary disabled:opacity-40 disabled:pointer-events-none"
                    title="{{ $quantity >= $available ? __('No more in stock') : __('Add to bundle') }}"
            >
                <i class="fa fa-plus"></i>
            </button>
        </div>
    @endif
</div>

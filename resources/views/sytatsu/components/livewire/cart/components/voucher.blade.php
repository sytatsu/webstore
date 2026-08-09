<div>
    @if ($this->cart->coupon_code)
        <div class="flex items-center justify-between p-3 text-sm bg-gray-50 dark:bg-slate-900 rounded-none">
            <span class="text-black dark:text-white">
                <i class="fa fa-tag text-primary mr-2"></i>
                {{ __('Discount code applied') }}: <span class="avenir-bold">{{ $this->cart->coupon_code }}</span>
            </span>

            <button type="button" wire:click="remove" wire:loading.attr="disabled" class="text-xs font-bold uppercase tracking-widest text-gray-500 hover:text-red-500 transition-colors">
                {{ __('Remove') }}
            </button>
        </div>
    @else
        <form wire:submit.prevent="apply" class="flex items-start gap-2">
            <div class="flex-1">
                <x-ui.input.default.input
                    id="code"
                    wire:model="code"
                    type="text"
                    placeholder="{{ __('Discount Code') }}"
                />
            </div>

            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="apply"
                    class="py-3 px-4 text-xs font-bold avenir-bold uppercase tracking-widest text-slate-600 dark:text-slate-400 border-1 border-slate-300 dark:border-slate-600 hover:text-white hover:border-slate-500 hover:bg-slate-500 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none">
                {{ __('Apply') }}
            </button>
        </form>
    @endif
</div>

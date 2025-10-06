<div class="flex-grow">
    @if ($this->cart && $this->lines)
        <div class="flow-root">
            <ul class="-mt-4 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-500{{ $this->checkout ? "" : " max-h-96" }}">
                @foreach ($this->lines as $index => $line)
                    <li>
                        <div class="flex py-4" wire:key="line_{{ $line['id'] }}">
                            <img class="object-cover aspect-square w-16 h-16 rounded"
                                 src="{{ $line['thumbnail'] ?? \App\Services\WebstoreHelperService::productPlaceholderImage() }}">

                            <div class="flex-1 ml-4">
                                <a href="{{ \App\Services\WebstoreHelperService::getProductRoute($line['purchasable']->product, ['purchasable_id' => $line['purchasable']->id]) }}" class="max-w-[20ch] text-sm font-medium text-black dark:text-white hover:underline">
                                    <span class="font-bold">{{ $line['description'] }}</span>

                                    @if($line['options'])
                                        <span> - {{ __($line['options']) }}</span>
                                    @endif
                                </a>

                                <div class="flex items-center mt-2">
                                    {{-- @TODO; Loader on change --}}
                                    <div class="flex rounded-lg border border-gray-100 dark:border-slate-800">
                                        <button type="button" class="size-8 m-0 inline-flex justify-center items-center gap-x-2 text-xs font-semibold rounded-s-md border border-transparent text-black dark:text-white bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                                                wire:click.prevent="incrementLine('{{ $index }}')"
                                            @disabled(($line['purchasable']->purchasable === 'in_stock' && $line['purchasable']->stock <= $line['quantity']) || $this->isCartDisabled())>
                                            <i class="fa fa-plus"></i>
                                        </button>

                                        <input class="size-8 text-xs text-center transition-colors border border-transparent text-black dark:text-white bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 [&::-webkit-inner-spin-button]:appearance-none focus:outline-none"
                                               type="number"
                                               id="quantity"
                                               min="1"
                                               value="1"
                                               wire:model.debounce="lines.{{ $index }}.quantity"
                                               wire:change="updateLines"
                                               wire:loading.attr="disabled"
                                               @disabled($this->isCartDisabled())
                                        />

                                        <button type="button" class="size-8 m-0 inline-flex justify-center items-center gap-x-2 text-xs font-semibold rounded-e-md border border-transparent text-black dark:text-white bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                                                wire:click.prevent="decrementLine('{{ $index }}')" @disabled(($line['quantity'] <= 1 || $this->isCartDisabled()))>
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>

                                    <p class="ml-2 text-xs text-place dark:text-white">
                                        @ {{ $line['unit_price'] }}
                                    </p>

                                    @if (!$this->isCartDisabled())
                                        <button class="p-2 ml-auto text-gray-600 transition-colors rounded-lg hover:bg-gray-100 hover:text-gray-700 text-black dark:text-white"
                                                type="button"
                                                wire:click="removeLine('{{ $line['id'] }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="w-4 h-4"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($errors->get('lines.' . $index . '.quantity'))
                            <div class="p-2 mb-4 text-xs font-medium text-center text-red-700 rounded bg-red-50"
                                 role="alert">
                                @foreach ($errors->get('lines.' . $index . '.quantity') as $error)
                                    {{ __($error) }}
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <hr class="text-gray-300 dark:text-gray-400">

        <div class="divide-y divide-gray-200 dark:divide-gray-500">
            @if ($this->checkout)
                <dl class="flex flex-wrap py-2 text-sm">
                    <dt class="w-1/2 font-medium text-black dark:text-white">
                        {{ __('Sub-total') }}
                    </dt>

                    <dd class="w-1/2 text-right text-black dark:text-white">
                        {{ $this->cart->subTotal->formatted() }}
                    </dd>
                </dl>

                <dl class="flex flex-wrap py-2 text-sm">
                    <dt class="w-1/2 font-medium text-black dark:text-white">
                        {{ __('Tax') }}
                    </dt>

                    <dd class="w-1/2 text-right text-black dark:text-white">
                        {{ $this->cart->taxTotal->formatted() }}
                    </dd>
                </dl>
            @endif

            <dl class="flex flex-wrap py-2 text-sm">
                <dt class="w-1/2 font-medium text-black dark:text-white my-auto">
                    {{ __('Shipping costs') }}
                </dt>

                <dd class="flex flex-col w-1/2 text-right text-black dark:text-white">
                    @if ($this->shippingOption)
                        <span>{{ $this->getShippingOptionProperty('name') }}</span>
                        <span>{{ $this->getShippingOptionProperty('price')->formatted() }}</span>
                    @else
                        {{ __('Unknown') }}
                    @endif
                </dd>
            </dl>

            <dl class="flex flex-wrap pt-4">
                <dt class="w-1/2 font-medium text-black dark:text-white">
                    {{ __('Total') }}
                </dt>

                <dd class="w-1/2 text-right text-black dark:text-white">
                    {{ $this->cart->total->formatted() }}
                </dd>
            </dl>
        </div>
    @else
        <p class="py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-300">
            {{ __('Your cart is empty') }}
        </p>
    @endif

    @if (!((!($this->cart && $this->lines) && $this->checkout) || $this->isCartDisabled()))
        <div class="mt-4 space-y-4 text-center">
            <x-ui.button.default.primary class="w-full" href="{{ route('sytatsu.webstore.cart') }}">
                {{ __('Overview') }}
            </x-ui.button.default.primary>
        </div>

        <div class="mt-4 space-y-4 text-center">
            <x-ui.button.default.secondary class="w-full" href="{{ route('sytatsu.webstore.checkout') }}">
                {{ __('Checkout') }}
            </x-ui.button.default.secondary>
        </div>
   @endif
</div>

<div class="sm:relative"
     x-data="{
         cartOpen: @entangle('cartOpen')
     }">

    {{-- @TODO; Make component themed (dark mode) --}}
    {{-- @TODO; Make component translatable --}}
    {{-- @TODO; Make component fit the current style --}}

    <button class="relative grid w-16 h-16 transition hover:opacity-75  m-auto"
            x-on:click="cartOpen = !cartOpen">
        <span class="sr-only">
            Cart
        </span>
        <div class="place-self-center">
            <i class="fa fa-cart-shopping text-size-lg text-slate-800 dark:text-white"></i>
            @if ($this->cartTotalQuantity > 0)
                <div class="absolute py-0.5 px-1 top-2 right-2 text-size-sm bg-secondary rounded-full aspect-square text-white">
                    {{ $this->cartTotalQuantity }}
                </div>
            @endif
        </div>
    </button>

    <div class="absolute inset-x-0 top-auto z-50 w-screen max-w-sm px-6 py-8 mx-auto mt-8 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-lg dark:shadow-primary/30 sm:left-auto rounded-xl"
         x-show="cartOpen"
         x-on:click.away="cartOpen = false"
         x-transition
         x-cloak>
        <button class="absolute text-gray-500 dark:text-gray-100 transition-transform top-3 right-3 hover:scale-110"
                type="button"
                aria-label="Close"
                x-on:click="cartOpen = false">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div>
            @if ($this->cart && $lines)
                <div class="flow-root">
                    <ul class="-my-4 overflow-y-auto divide-y divide-gray-500 max-h-96">
                        @foreach ($lines as $index => $line)
                            <li>
                                <div class="flex py-4" wire:key="line_{{ $line['id'] }}">
                                    <img class="object-cover aspect-square w-16 h-16 rounded"
                                         src="{{ $line['thumbnail'] ?? Vite::asset('resources/images/product_placeholder.jpg') }}">

                                    <div class="flex-1 ml-4">
                                        {{-- @TODO; Option does not get translated correctly --}}
                                        <a href="{{ \App\Services\WebstoreHelperService::getProductRoute($line['purchasable']->product, ['option_id' => $line['option_id']]) }}" class="max-w-[20ch] text-sm font-medium text-black dark:text-white hover:underline">
                                            <span class="font-bold">{{ $line['description'] }}</span>

                                            @if($line['options']) - {{ __($line['options']) }}@endif
                                        </a>

                                        <div class="flex items-center mt-2">

                                            {{-- @TODO; Alter to be correct size and correct function --}}
                                            <div class="flex rounded-lg border border-gray-100 dark:border-slate-800">
                                                <button type="button" class="size-8 m-0 inline-flex justify-center items-center gap-x-2 text-xs font-semibold rounded-s-md border border-transparent text-black dark:text-white bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                                                        wire:click.prevent="incrementLine('{{ $index }}')"
                                                        @disabled($line['purchasable']->purchasable === 'in_stock' && $line['purchasable']->stock <= $line['quantity'])>
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
                                                />

                                                <button type="button" class="size-8 m-0 inline-flex justify-center items-center gap-x-2 text-xs font-semibold rounded-e-md border border-transparent text-black dark:text-white bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                                                        wire:click.prevent="decrementLine('{{ $index }}')" @disabled($line['quantity'] <= 1)>
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>

                                            <p class="ml-2 text-xs text-place dark:text-white">
                                                @ {{ $line['unit_price'] }}
                                            </p>

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
                                        </div>
                                    </div>
                                </div>

                                @if ($errors->get('lines.' . $index . '.quantity'))
                                    <div class="p-2 mb-4 text-xs font-medium text-center text-red-700 rounded bg-red-50"
                                         role="alert">
                                        @foreach ($errors->get('lines.' . $index . '.quantity') as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <dl class="flex flex-wrap pt-4 mt-6 text-sm border-t border-gray-100 dark:border-gray-500">
                    <dt class="w-1/2 font-medium text-black dark:text-white">
                        Sub-Totaal
                    </dt>

                    <dd class="w-1/2 text-right text-black dark:text-white">
                        {{ $this->cart->subTotal->formatted() }}
                    </dd>
                </dl>
            @else
                <p class="py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-300">
                    Je winkelmandje is leeg
                </p>
            @endif
        </div>

        @if ($this->cart && $this->lines)
            <div class="mt-4 space-y-4 text-center">
                <a class="block w-full p-3 text-sm font-medium text-center text-white bg-primary-dark rounded-lg hover:bg-primary"
                   {{-- @TODO; Add checkout route --}}
{{--                   href="{{ route('checkout.view') }}"--}}
                >
                    Afrekenen
                </a>
            </div>
        @endif
    </div>
</div>

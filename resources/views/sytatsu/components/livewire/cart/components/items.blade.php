<div>
    <x-ui.spinner-overlay wire:loading.flex wire:target="incrementLine, decrementLine, removeLine, updateLines" />
    <ul class="-mt-4 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-500{{ ($this->checkout || Route::currentRouteName() === 'sytatsu.webstore.cart') ? "" : " max-h-96" }}">
        @foreach ($this->lines as $index => $line)
        <li class="relative">
            <div class="flex py-4" wire:key="line_{{ $line['id'] }}">
                <img class="object-cover aspect-square {{ Route::currentRouteName() === 'sytatsu.webstore.cart' ? 'w-24 h-24' : 'w-16 h-16' }} rounded"
                     src="{{ $line['thumbnail'] ?? \App\Services\WebstoreHelperService::productPlaceholderImage() }}">

                <div class="flex-1 ml-4">
                    <div class="flex flex-row justify-between text-sm font-medium text-black dark:text-white">
                        <a href="{{ \App\Services\WebstoreHelperService::getProductRoute($line['purchasable']->product, ['purchasable_id' => $line['purchasable']->id]) }}" class="{{ Route::currentRouteName() === 'sytatsu.webstore.cart' ? 'max-w-[40ch]' : 'max-w-[20ch]' }} hover:underline">
                            <span class="font-bold">{{ $line['description'] }}</span>

                            @if($line['options'])
                                <span> - {{ __($line['options']) }}</span>
                            @endif
                        </a>

                        <span>{{ $line['sub_total'] }}</span>
                    </div>

                    <div class="flex items-center mt-2">
                        <div class="flex rounded-none bg-gray-50 dark:bg-slate-900">
                            @if (!$this->isCartDisabled())
                                @php
                                    $isIncrementDisabled = ($line['purchasable']->purchasable === 'in_stock' && $line['purchasable']->stock <= $line['quantity']);
                                @endphp
                                <button type="button" class="size-8 m-0 inline-flex justify-center items-center gap-x-2 text-xs font-semibold border border-transparent text-black dark:text-white bg-transparent hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                                        wire:click.prevent="incrementLine('{{ $index }}')"
                                        {{ $isIncrementDisabled ? 'disabled' : '' }}>
                                    <i class="fa fa-plus"></i>
                                </button>
                            @endif

                            <input class="size-8 text-xs text-center transition-colors text-black dark:text-white bg-transparent hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 [&::-webkit-inner-spin-button]:appearance-none focus:outline-none"
                                   type="number"
                                   id="quantity"
                                   min="1"
                                   value="1"
                                   wire:model.debounce="lines.{{ $index }}.quantity"
                                   wire:change="updateLines"
                                   wire:loading.attr="disabled"
                                   {{ $this->isCartDisabled() ? 'disabled' : '' }}
                            />

                            @if (!$this->isCartDisabled())
                                <button type="button" class="size-8 m-0 inline-flex justify-center items-center gap-x-2 text-xs font-semibold border border-transparent text-black dark:text-white bg-transparent hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                                        wire:click.prevent="decrementLine('{{ $index }}')"
                                        {{ $line['quantity'] <= 1 ? 'disabled' : '' }}>
                                    <i class="fa fa-minus"></i>
                                </button>
                            @endif
                        </div>

                        <p class="ml-2 text-xs dark:text-white">
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

            <x-ui.field-error :field="'lines.' . $index . '.quantity'" class="mb-4 mt-0" />
        </li>
    @endforeach
    </ul>
</div>

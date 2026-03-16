<div class="flex-grow">
    <div class="flow-root">
        <ul class="-mt-4 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-500">
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
                                <div class="flex rounded-lg border border-gray-100 dark:border-slate-800">
                                    <input class="size-8 text-xs text-center transition-colors border border-transparent text-black dark:text-white bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 [&::-webkit-inner-spin-button]:appearance-none focus:outline-none"
                                           type="number" value="{{ $line['quantity'] }}" disabled
                                    />
                                </div>

                                <p class="ml-2 text-xs text-place dark:text-white">
                                    @ {{ $line['unit_price'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <hr class="text-gray-300 dark:text-gray-400">

    <div class="divide-y divide-gray-200 dark:divide-gray-500">
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

        <dl class="flex flex-wrap py-2 text-sm">
            <dt class="w-1/2 font-medium text-black dark:text-white my-auto">
                {{ __('Shipping costs') }}
            </dt>

            <dd class="flex flex-col w-1/2 text-right text-black dark:text-white">
                @if ($this->shippingOption)
                    <span>{{ $this->shippingOption->getName() }}</span>
                    <span>{{ $this->shippingOption->getPrice()->formatted() }}</span>
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
</div>

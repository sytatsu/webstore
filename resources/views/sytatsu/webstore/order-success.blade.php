<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full px-4 md:px-6 lg:px-8 flex flex-col justify-center items-center">
    <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 w-full max-w-2xl text-center flex flex-col gap-6">
        <span class="text-6xl" role="img">🥳</span>

        <h1 class="text-3xl font-bold text-black dark:text-white avenir-bold uppercase">
           {{ __('Order has been placed') }}
        </h1>

        <hr class="border-gray-200 dark:border-gray-500">

        <p class="font-medium text-lg text-black dark:text-white">
            @if($order)
                {{ __('Your order reference number is') }} <strong class="underline">#{{ $order->reference }}</strong>
            @else
                {{ __('Your order has been placed successfully.') }}
            @endif
        </p>

        <p class="text-slate-600 dark:text-gray-400">
            {{ __('An email confirmation has been sent to the given e-mail, it may take a few minutes to arrive') }}
        </p>

        @if($order)
            <div class="mt-6 text-left border-t border-gray-200 dark:border-gray-500 pt-6">
                <h2 class="text-xl font-bold text-black dark:text-white mb-4 uppercase avenir-bold">
                    {{ __('Order Details') }}
                </h2>
                <div class="space-y-4">
                    @foreach($order->lines as $line)
                        @if($line->purchasable_type !== \Lunar\DataTypes\ShippingOption::class)
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center overflow-hidden rounded">
                                        @if($line->purchasable && method_exists($line->purchasable, 'getThumbnail') && $line->purchasable->getThumbnail())
                                            <img src="{{ $line->purchasable->getThumbnail()->getUrl('small') }}" alt="{{ $line->description }}" class="object-cover w-full h-full">
                                        @else
                                            <span class="text-xs text-gray-400">No image</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-black dark:text-white leading-tight text-left">
                                            @if($line->purchasable && $line->purchasable->product)
                                                <a href="{{ route('sytatsu.webstore.product', $line->purchasable->product->defaultUrl->slug) }}" class="hover:underline text-primary">
                                                    {{ $line->description }}
                                                </a>
                                            @else
                                                {{ $line->description }}
                                            @endif
                                        </p>
                                        @if($line->option)
                                            <p class="text-sm text-slate-600 dark:text-gray-400 italic text-left">{{ $line->option }}</p>
                                        @endif
                                        <p class="text-sm text-slate-600 dark:text-gray-400 text-left">{{ __('Quantity') }}: {{ $line->quantity }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-black dark:text-white">{{ $line->sub_total->formatted }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-500 space-y-2">
                    <div class="flex justify-between text-slate-600 dark:text-gray-400">
                        <span>{{ __('Subtotal') }}</span>
                        <span>{{ $order->sub_total->formatted }}</span>
                    </div>
                    @if($order->shipping_total->value > 0)
                        <div class="flex justify-between text-slate-600 dark:text-gray-400">
                            <span>{{ __('Shipping') }}</span>
                            <span>{{ $order->shipping_total->formatted }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-600 dark:text-gray-400">
                        <span>{{ __('Tax') }}</span>
                        <span>{{ $order->tax_total->formatted }}</span>
                    </div>
                    <div class="flex justify-between text-xl avenir-bold text-black dark:text-white pt-2">
                        <span>{{ __('Total') }}</span>
                        <span>{{ $order->total->formatted }}</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-8">
            <x-ui.button.default.primary href="{{ route('sytatsu.webstore.welcome') }}">
                {{ __('Go back to the store') }}
            </x-ui.button.default.primary>
        </div>
    </div>
</div>

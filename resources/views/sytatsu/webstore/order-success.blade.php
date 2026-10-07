<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full flex flex-col justify-center items-center">
    <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 w-full max-w-2xl text-center flex flex-col gap-6">
        <span class="text-6xl" role="img">🥳</span>

        <div class="divide-y divide-gray-200 dark:divide-gray-500">
            <h1 class="pb-6 text-3xl font-bold text-black dark:text-white avenir-bold uppercase">
               {{ __('Order has been placed') }}
            </h1>

            <p class="pt-6 font-medium text-lg text-black dark:text-white">
                @if($order)
                    {{ __('Your order reference number is') }} <strong class="underline">#{{ $order->reference }}</strong>
                @else
                    {{ __('Your order has been placed successfully.') }}
                @endif
            </p>
        </div>

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
                            @php($bundle = $line->meta['bundle'] ?? null)
                            <div>
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        {{-- A bundle line's own purchasable is the hidden pricing
                                             variant, which has no thumbnail of its own — the
                                             picked-items strip below already carries the
                                             imagery, so this box is skipped entirely rather
                                             than showing an empty "No image" placeholder. --}}
                                        @unless($bundle)
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center overflow-hidden rounded">
                                                @if($line->purchasable && method_exists($line->purchasable, 'getThumbnail') && $line->purchasable->getThumbnail())
                                                    <img src="{{ $line->purchasable->getThumbnail()->getUrl('small') }}" alt="{{ $line->description }}" class="object-cover w-full h-full">
                                                @else
                                                    <span class="text-xs text-gray-400">No image</span>
                                                @endif
                                            </div>
                                        @endunless
                                        <div>
                                            <p class="font-bold text-black dark:text-white leading-tight text-left">
                                                @if(!$bundle && $line->purchasable && $line->purchasable->product)
                                                    <a href="{{ route('sytatsu.webstore.product', ['product' => $line->purchasable->product->defaultUrl->slug]) }}" class="hover:underline text-primary">
                                                        {{ $line->description }}
                                                    </a>
                                                @else
                                                    {{ $bundle['name'] ?? $line->description }}
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

                                {{-- Which products the bundle actually contains — the same
                                     information the cart's own bundle line shows
                                     (cart/components/items.blade.php), minus the Edit link,
                                     since this page is a read-only receipt. --}}
                                @if($bundle)
                                    <div class="flex flex-wrap items-center gap-1 mt-3 ml-20">
                                        @foreach($bundle['items'] ?? [] as $item)
                                            <div class="relative size-9 flex-shrink-0 rounded-md border border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-900"
                                                 title="{{ $item['name'] ?? '' }}"
                                                 x-data="{ tipShown: false, tipX: 0, tipY: 0 }"
                                                 @mouseenter="tipShown = true; const r = $el.getBoundingClientRect(); tipX = r.left + r.width / 2; tipY = r.top"
                                                 @mouseleave="tipShown = false"
                                            >
                                                <img class="object-contain w-full h-full p-0.5 rounded-md"
                                                     src="{{ $item['thumbnail'] ?? \App\Services\WebstoreHelperService::productPlaceholderImage() }}"
                                                     alt="{{ $item['name'] ?? '' }}">
                                                <div x-show="tipShown" x-cloak
                                                     class="fixed z-[100] -translate-x-1/2 -translate-y-full px-2 py-1 rounded-md bg-slate-900 text-white text-[11px] whitespace-nowrap shadow-lg pointer-events-none"
                                                     :style="`left: ${tipX}px; top: ${tipY - 6}px`"
                                                     style="display: none;"
                                                >{{ $item['name'] ?? '' }}</div>
                                                <span class="absolute -top-1.5 -right-1.5 bg-primary text-white text-[9px] font-bold size-4 rounded-full flex items-center justify-center avenir-bold leading-none">
                                                    {{ $item['quantity'] ?? 1 }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
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

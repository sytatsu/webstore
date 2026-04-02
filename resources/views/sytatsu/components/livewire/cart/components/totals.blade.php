<div class="divide-y divide-gray-200 dark:divide-gray-500">
    <dl class="flex flex-wrap py-2 text-sm">
        <dt class="w-1/2 font-medium text-black dark:text-white">
            {{ __('Sub-total') }}
        </dt>

        <dd class="w-1/2 text-right text-black dark:text-white">
            {{ $this->cart->subTotal->formatted() }}
        </dd>
    </dl>

    @if ($this->cart->discountTotal?->value > 0)
        <dl class="flex flex-wrap py-2 text-sm">
            <dt class="w-1/2 font-medium text-black dark:text-white">
                {{ __('Discount') }}
            </dt>

            <dd class="w-1/2 text-right text-black dark:text-white">
                -{{ $this->cart->discountTotal->formatted() }}
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

    <dl class="flex flex-wrap py-2 text-sm">
        <dt class="w-1/2 font-medium text-black dark:text-white">
            {{ __('Tax') }}
        </dt>

        <dd class="w-1/2 text-right text-black dark:text-white">
            {{ $this->cart->taxTotal->formatted() }}
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

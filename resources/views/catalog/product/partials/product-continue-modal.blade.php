<x-modal name="confirm-product-continue">
    <form class="flex flex-col p-8" action="{{ route('catalog.products.continue') }}" method="post">
        @csrf @method('post')
        <input type="hidden" name="uuid" value="{{ $product->uuid }}">

        <div class="flex flex-col gap-6 pb-6">
            <h2 class="text-lg bg-green-600 rounded-lg text-white py-4 px-8 avenir">Are you sure you want to continue the product <b>"{{ $product->name }}"</b>?</h2>
            <ul class="list-disc pl-6 text-start">
                <li>All restrictions will be lifted</li>
            </ul>
        </div>

        <div class="flex flex-row justify-between">
            <x-secondary-button x-data="" x-on:click.prevent="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-success-button type="submit" >
                <i class="fa fa-check-circle pr-1"></i>{{ __('Confirm continue') }}
            </x-success-button>
        </div>
    </form>
</x-modal>

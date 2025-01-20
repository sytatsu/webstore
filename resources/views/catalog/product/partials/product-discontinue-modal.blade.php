<x-modal name="confirm-product-discontinue">
    <form class="flex flex-col p-8" action="{{ route('catalog.products.discontinue') }}" method="post">
        @csrf @method('post')
        <input type="hidden" name="uuid" value="{{ $product->uuid }}">

        <div class="flex flex-col gap-6 pb-6">
            <h2 class="text-lg bg-yellow-400 rounded-lg text-black py-4 px-8 avenir">Are you sure you want to discontinue the product <b>"{{ $product->name }}"</b>?</h2>
            <ul class="list-disc pl-6 text-start">
                <li>Discontinued items will be showed at the bottom of lists</li>
                <li>Indicator will be present to show that the product is discontinued</li>
                <li>Discontinued items will still be able to be edited</li>
                <li>Discontinued items will still be prevent within counts</li>
                <li>Discontinued items have the ability to continue again which in turn lifts all restrictions</li>
            </ul>
        </div>

        <div class="flex flex-row justify-between">
            <x-secondary-button x-data="" x-on:click.prevent="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-warning-button type="submit" >
                <i class="fa fa-ban pr-1"></i>{{ __('Confirm discontinue') }}
            </x-warning-button>
        </div>
    </form>
</x-modal>

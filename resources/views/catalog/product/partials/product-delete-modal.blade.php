<x-modal name="confirm-product-deletion">
    <form class="flex flex-col p-8" action="{{ route('catalog.products.delete') }}" method="post">
        @csrf @method('delete')
        <input type="hidden" name="uuid" value="{{ $product->uuid }}">

        <div class="flex flex-col gap-6 pb-6">
            <h2 class="text-lg bg-red-500 rounded-lg text-white py-4 px-8 avenir">Are you sure you want to delete the product <b>"{{ $product->name }}"</b>?</h2>
            <ul class="list-disc pl-6 text-start">
                <li>All variants will be deleted</li>
                <li>This action cannot be un-done</li>
            </ul>
        </div>

        <div class="flex flex-row justify-between">
            <x-secondary-button x-data="" x-on:click.prevent="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button type="submit" >
                <i class="fa fa-trash pr-1"></i>{{ __('Confirm delete') }}
            </x-danger-button>
        </div>
    </form>
</x-modal>

<x-modal name="confirm-brand-deletion">
    <div class="flex flex-col p-8">
        <div class="flex flex-col gap-6 pb-6">
            <h2 class="text-lg bg-red-500 rounded-lg text-white py-4 px-8 avenir">Are you sure you want to delete the brand <b>"{{ $brand->name }}"</b>?</h2>
            <ul class="list-disc pl-6">
                <li>All linked items will be set to null</li>
                <li>This action cannot be un-done</li>
            </ul>
        </div>

        <div class="flex flex-row justify-between">
            <x-secondary-button x-data="" x-on:click.prevent="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button :href="route('warehouse.brands.delete', $brand)">
                <i class="fa fa-trash pr-1"></i>{{ __('Confirm delete') }}
            </x-danger-button>
        </div>
    </div>
</x-modal>

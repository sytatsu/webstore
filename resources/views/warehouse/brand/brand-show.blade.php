<x-layouts.warehouse.brand-layout>
    <div class="p-6 space-y-6 flex flex-col">
        <div class="flex flex-row justify-between py-1">
            <div class="w-1/3 p-4">
                <span class='text-md text-2xl avenir-bold'>{{ $brand->name }}</span>
            </div>

           <div class="w-1/3 text-end p-4">
               <x-secondary-button-link :href="route('warehouse.brands.edit', ['brand' => $brand, 'action' => \App\Enums\Actions\SaveAndAction::TO_MODEL])">
                   <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
               </x-secondary-button-link>

               <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-brand-deletion')">
                   <i class="fa fa-trash pr-2"></i>{{ __('Delete') }}
               </x-danger-button>
           </div>
        </div>

        <div class="flex flex-col p-4 rounded bg-slate-100 shadow">
            <span class='block font-medium text-sm text-gray-700'>{{ __("Description") }}</span>
            <span class='text-md'>{{ $brand->description }}</span>
        </div>

        <div class="flex flex-row justify-between py-1">
            <div class="w-1/3 p-4 rounded bg-slate-100 shadow">
                <span class='block font-medium text-sm text-gray-700'>{{ __("Used by") }}</span>
                <ul class='text-md list-disc pl-4'>
                    <li>{{ $brand->productCount() }} Product(s)</li>
                </ul>
            </div>

            <div class="w-1/3 text-end self-end p-4 rounded bg-slate-100 shadow">
                <div class="pb-1">
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Created at") }}</span>
                    <span class='text-md'>{{ $brand->createdAt }}</span>
                </div>
                <div>
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Updated at") }}</span>
                    <span class='text-md'>{{ $brand->updatedAt }}</span>
                </div>
            </div>
        </div>

{{--        @if ($brand->products->count() > 0)--}}
{{--            @foreach($brand->products as $product)--}}
{{--                {{ $brand->$product }}--}}
{{--            @endforeach--}}
{{--        @endif--}}
    </div>

    <x-modal name="confirm-brand-deletion">
        <div class="flex flex-col p-8">
            <div class="flex flex-col gap-2 pb-6">
                <h2 class="text-lg">Are you sure you want to delete the brand <b>"{{ $brand->name }}"</b>?</h2>
                <ul class="list-disc pl-3">
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
</x-layouts.warehouse.brand-layout>

<x-layouts.warehouse.variant-layout>
    <div class="p-6 space-y-6 flex flex-col">
        <div class="flex flex-row justify-between p-1 bg-slate-700 text-white rounded-lg align-middle">
            <div class="p-4 my-auto">
                <span class='text-2xl avenir-bold'>
                    {{ $variant->name }}
                </span>
            </div>

           <div class="text-end p-4">
               <x-secondary-button-link :href="route('warehouse.variants.edit', ['variant' => $variant, 'action' => \App\Enums\Actions\SaveAndAction::TO_MODEL->toString()])">
                   <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
               </x-secondary-button-link>

               <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-variant-deletion')">
                   <i class="fa fa-trash pr-2"></i>{{ __('Delete') }}
               </x-secondary-button>
           </div>
        </div>

        <div class="flex flex-col p-4 rounded-lg bg-slate-100 shadow">
            <span class='block font-medium text-sm text-gray-700'>{{ __("Description") }}</span>
            <span class='text-md'>{{ $variant->description }}</span>
        </div>

        <div class="flex flex-row gap-8 py-1">
            <div class="p-4 rounded-lg bg-slate-100 shadow grow">
                <span class='block font-medium text-sm text-gray-700'>{{ __("Used by") }}</span>
                <ul class='text-md list-disc pl-4'>
                    <li>{{ $variant->productVariantCount() }} Product Variant(s)</li>
                </ul>
            </div>

            <div class="text-end self-end p-4 rounded-lg bg-slate-100 shadow grow">
                <div class="pb-1">
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Created at") }}</span>
                    <span class='text-md'>{{ $variant->createdAt }}</span>
                </div>
                <div>
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Updated at") }}</span>
                    <span class='text-md'>{{ $variant->updatedAt }}</span>
                </div>
            </div>
        </div>
    </div>

    @include('warehouse.variant.partials.variant-delete-modal', $variant)
</x-layouts.warehouse.variant-layout>

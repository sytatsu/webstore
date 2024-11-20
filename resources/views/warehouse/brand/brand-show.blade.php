<x-layouts.warehouse.brand-layout>
    <x-section width="w-full" class="!p-2">
        <div class="p-6 space-y-6 flex flex-col">
            <x-section-header>
                {{ __($brand->name) }}

                <x-slot name="actions">
                    <div class="text-end p-4">
                        <x-secondary-button-link :href="route('warehouse.brands.edit', ['brand' => $brand, 'action' => \App\Enums\Actions\SaveAndAction::TO_MODEL->toString()])">
                            <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
                        </x-secondary-button-link>

                        <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-brand-deletion')">
                            <i class="fa fa-trash pr-2"></i>{{ __('Delete') }}
                        </x-secondary-button>
                    </div>
                </x-slot>
            </x-section-header>

            <div class="flex flex-col p-4 rounded-lg bg-slate-100 shadow">
                <span class='block font-medium text-sm text-gray-700'>{{ __("Description") }}</span>
                <span class='text-md'>{{ $brand->description }}</span>
            </div>

            <div class="flex flex-row gap-8 py-1">
                <div class="p-4 rounded-lg bg-slate-100 shadow grow">
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Used by") }}</span>
                    <ul class='text-md list-disc pl-4'>
                        <li>{{ $brand->productCount() }} Product(s)</li>
                    </ul>
                </div>

                <div class="text-end self-end p-4 rounded-lg bg-slate-100 shadow grow">
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
    </x-section>

    @include('warehouse.brand.partials.brand-delete-modal', $brand)
</x-layouts.warehouse.brand-layout>

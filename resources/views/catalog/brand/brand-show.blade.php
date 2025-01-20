<x-layouts.catalog.brand-layout>
    <x-section width="w-full" class="!p-2">
        <div class="p-6 space-y-6 flex flex-col">
            <x-section-header>
                {{ __($brand->name) }}

                <x-slot name="actions">
                    <div class="text-end p-4">
                        <x-secondary-button-link :href="route('catalog.brands.edit', ['brand' => $brand, 'action' => \App\Enums\Actions\SaveAndAction::TO_MODEL->toString()])">
                            <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
                        </x-secondary-button-link>

                        <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-brand-deletion')">
                            <i class="fa fa-trash pr-2"></i>{{ __('Delete') }}
                        </x-secondary-button>
                    </div>
                </x-slot>
            </x-section-header>

            <div class="px-2">
                <x-data-field :title="__('Description')">
                    @if ($brand->descriptopm)
                        {{ $brand->description }}
                    @else
                        <i class="muted">No description available</i>
                    @endif
                </x-data-field>

                <hr class="m-2" />

                <div class="flex flex-row pt-4 flex-wrap">
                    <x-data-field :title="__('Used by')" class="w-1/3">
                        {{ $brand->productCount() }} Product(s)
                    </x-data-field>

                    <x-data-field :title="__('Created at')" class="w-1/3">
                        {{ $brand->createdAt }}
                    </x-data-field>

                    <x-data-field :title="__('Updated at')" class="w-1/3">
                        {{ $brand->updatedAt }}
                    </x-data-field>
                </div>
            </div>
        </div>
    </x-section>

    @include('catalog.brand.partials.brand-delete-modal', $brand)
</x-layouts.catalog.brand-layout>

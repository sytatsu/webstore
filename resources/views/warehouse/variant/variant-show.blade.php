<x-layouts.warehouse.variant-layout>
    <x-section width="w-full" innerClass="space-y-6 flex-col">
        <x-section-header>
            {{ __($variant->name) }}

            <x-slot name="actions">
                <div class="text-end p-4">
                    <x-secondary-button-link :href="route('warehouse.variants.edit', ['variant' => $variant, 'action' => \App\Enums\Actions\SaveAndAction::TO_MODEL->toString()])">
                        <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
                    </x-secondary-button-link>

                    <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-variant-deletion')">
                        <i class="fa fa-trash pr-2"></i>{{ __('Delete') }}
                    </x-secondary-button>
                </div>
            </x-slot>
        </x-section-header>

        <div class="px-2">
            <x-data-field :title="__('Description')">
                @if ($variant->descriptopm)
                    {{ $variant->description }}
                @else
                    <i class="muted">No description available</i>
                @endif
            </x-data-field>

            <hr class="m-2" />

            <div class="flex flex-row pt-4 flex-wrap">
                <x-data-field :title="__('Used by')" class="w-1/3">
                    <ul class='text-md list-disc pl-4'>
                        <li>{{ $variant->productVariantCount() }} Product Variant(s)</li>
                    </ul>
                </x-data-field>

                <x-data-field :title="__('Created at')" class="w-1/3">
                    {{ $variant->createdAt }}
                </x-data-field>

                <x-data-field :title="__('Updated at')" class="w-1/3">
                    {{ $variant->updatedAt }}
                </x-data-field>
            </div>
        </div>
    </x-section>

    @include('warehouse.variant.partials.variant-delete-modal', $variant)
</x-layouts.warehouse.variant-layout>

<x-layouts.warehouse.variant-layout>
    <x-section width="w-full" class="!p-2">
        <x-table>
            <x-slot name="header">
                <x-table.row-head>
                    <th class="pl-5"></th>
                    <th>Name</th>
                    <th>Used by</th>
                    <th class="pr-5 text-end"></th>
                </x-table.row-head>
            </x-slot>

            <x-slot name="content">
                @foreach($variants as $variant)
                    <x-table.row :checkbox="true">
                        <td>
                            <a class="w-full h-full avenir-bold underline text-secondary hover:text-secondary-dark"
                               href="{{ route('warehouse.variants.show', $variant) }}">{{ $variant->name }}</a>
                        </td>
                        <td>
                            <ul class="list-disc pl-4">
                                <li>{{ $variant->productVariantCount() }} Product Variant(s)</li>
                            </ul>
                        </td>

                        <x-slot name="actions">
                            <x-table.partials.action :href="route('warehouse.variants.show', $variant)">
                                <i class="fa fa-eye w-6"></i>Show
                            </x-table.partials.action>

                            <x-table.partials.action :href="route('warehouse.variants.edit', ['variant' => $variant, 'action' => \App\Enums\Actions\SaveAndAction::TO_OVERVIEW->name])">
                                <i class="fa fa-pen w-6"></i>Edit
                            </x-table.partials.action>
                        </x-slot>
                    </x-table.row>
                @endforeach
            </x-slot>
        </x-table>
    </x-section>
</x-layouts.warehouse.variant-layout>

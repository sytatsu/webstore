<x-layouts.warehouse.product-layout>
    <x-table>
        <x-slot name="header">
            <tr class="bg-gray-700 text-white h-12 text-left">
                <th class="pl-5">Name</th>
                <th></th>
                <th class="pr-5 text-end">Actions</th>
            </tr>
        </x-slot>

        <x-slot name="content">
            @foreach($products as $product)
                <tr>
                    @dump($product)
                </tr>
            @endforeach
        </x-slot>
    </x-table>
</x-layouts.warehouse.product-layout>

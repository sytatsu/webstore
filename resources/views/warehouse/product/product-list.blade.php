<x-layouts.warehouse.product-layout>
    <x-table>
        <x-slot name="header">
            <tr class="bg-gray-700 text-white h-12 text-left">
                <th class="pl-5"></th>
                <th>Name</th>
                <th>Product Type</th>
                <th>Brand</th>
                <th>Category</th>
                <th class="text-end pr-5">Variant Quantity</th>
                <th>Created at</th>
                <th>Updated at</th>
                <th class="pr-5 text-end">Actions</th>
            </tr>
        </x-slot>

        <x-slot name="content">
            @foreach($products as $product)
                <tr class="odd:bg-white even:bg-gray-50 border-b last:rounded-br-xl h-10">
                    <td>
                        <span class="px-4 hover:cursor-pointer">
                        <i class="fa fa-angle-right"></i>
                        </span>
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->productType->translation() }}</td>
                    <td>{{ $product->brand->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td class="text-end pr-5">{{ $product->productVariants->count() }}</td>
                    <td>{{ $product->createdAt->format('d-m-Y') }}</td>
                    <td>{{ $product->updatedAt->format('d-m-Y') }}</td>
                    <td class="text-end pr-5"><i class="fa fa-info-circle"></i></td>
                </tr>
            @endforeach
        </x-slot>
    </x-table>
</x-layouts.warehouse.product-layout>

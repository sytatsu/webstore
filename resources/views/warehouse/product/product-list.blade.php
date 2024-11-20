<x-layouts.warehouse.product-layout>
    <x-section width="w-full">
        <x-table>
            <x-slot name="header">
                <x-table.row-head>
                    <th class="pl-5"></th>
                    <th>Name</th>
                    <th>Product Type</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th class="text-end pr-5">Variant Quantity</th>
                    <th>Price (range)</th>
                    <th class="pr-5 text-end"></th>
                </x-table.row-head>
            </x-slot>

            <x-slot name="content">
                @foreach($products as $product)
                    <x-table.row checkbox="true">
                        <td>
                            <a class="w-full h-full avenir-bold underline text-secondary hover:text-secondary-dark" href="{{ route('warehouse.products.show', $product) }}">{{ $product->name }}</a>
                        </td>
                        <td>{{ $product->productType->translation() }}</td>
                        <td>
                            @if($product->brand)
                                <a class="hover:underline" href="{{ route('warehouse.brands.show', $product->brand) }}">{{ $product->brand->name }}</a>
                            @else
                                <span class="text-sm rounded-lg py-2 px-3 bg-red-500 text-white"><i class="fa fa-warning pr-1"></i>N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($product->category)
                                <a class="hover:underline" href="{{ route('warehouse.categories.show', $product->category) }}">{{ $product->category->name }}</a>
                            @else
                                <span class="text-sm rounded-lg py-2 px-3 bg-red-500 text-white"><i class="fa fa-warning pr-1"></i>N/A</span>
                            @endif
                        </td>
                        <td class="text-end pr-5">
                            {{ $productService->productVariantTypeCountToString($product) }}
                        </td>
                        <td>
                            {{ $productService->productVariantTypePriceToString($product) }}
                        </td>

                        <x-slot name="actions">
                            <x-table.partials.action :href="route('warehouse.products.show', $product)">
                                <i class="fa fa-eye w-6"></i>Show
                            </x-table.partials.action>

                            <x-table.partials.action :href="route('warehouse.products.edit', $product)">
                                <i class="fa fa-pen w-6"></i>Edit
                            </x-table.partials.action>
                        </x-slot>
                    </x-table.row>

    {{--            @TODO | Could be interesting for the future--}}
    {{--                @foreach($product->productVariants as $variant)--}}
    {{--                    <tr class="w-full h-5 border-b">--}}
    {{--                        <td colspan="9" class="px-6">--}}
    {{--                            {{ $variant->name }} | {{ $variant->price->formatted() }}--}}
    {{--                        </td>--}}
    {{--                    </tr>--}}
    {{--                @endforeach--}}

                @endforeach
            </x-slot>
        </x-table>
    </x-section>
</x-layouts.warehouse.product-layout>

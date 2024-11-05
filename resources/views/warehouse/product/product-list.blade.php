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
                <th>Price (range)</th>
                <th class="pr-5 text-end"></th>
            </tr>
        </x-slot>

        <x-slot name="content">
            @foreach($products as $product)
                <tr class="odd:bg-white even:bg-gray-50 border-b last:rounded-br-xl h-10">
                    <td class="px-2">
                        <div class="flex items-center">
                            <label for="checked-checkbox" class="hidden"></label>
                            <input id="checked-checkbox" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </td>
                    <td>
                        <a class="w-full h-full avenir-bold underline text-secondary hover:text-secondary-dark" href="{{ route('warehouse.product.show', $product) }}">{{ $product->name }}</a>
                    </td>
                    <td>{{ $product->productType->translation() }}</td>
                    <td>
                        @if($product->brand)
                            <a class="hover:underline" href="{{ route('warehouse.brands.show', $product->brand) }}">{{ $product->brand->name }}</a>
                        @else
                            <span class="text-red-500"><i class="fa fa-warning pr-1"></i>N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($product->category)
                            <a class="hover:underline" href="{{ route('warehouse.categories.show', $product->category) }}">{{ $product->category->name }}</a>
                        @else
                            <span class="text-red-500"><i class="fa fa-warning pr-1"></i>N/A</span>
                        @endif
                    </td>
                    <td class="text-end pr-5">
                        {{ $product->productVariantType  === \App\Enums\ProductVariantType::UNIQUE
                            ? $product->productVariantType->translation()
                            : $product->productVariants->count() }}
                    </td>
                    <td>
                        {{ $product->productVariantType  === \App\Enums\ProductVariantType::UNIQUE || $product->productVariants->count() <= 1
                            ? $product->productVariants->first()?->price->formatted() ?? 'N/A'
                            : $product->productVariants->sortBy('price')->first()?->price->formatted() . ' - ' . $product->productVariants->sortByDesc('price')->first()?->price->string() }}
                    </td>
                    <td class="text-end pr-2">
                        <x-dropdown align="right" width="36">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <i class="fa fa-ellipsis"></i>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('warehouse.product.show', $product)">
                                    <i class="fa fa-eye w-6"></i>Show
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('warehouse.product.edit', $product)">
                                    <i class="fa fa-pen w-6"></i>Edit
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </td>
                </tr>
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
</x-layouts.warehouse.product-layout>

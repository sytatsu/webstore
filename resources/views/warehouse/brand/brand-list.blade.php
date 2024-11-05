<x-layouts.warehouse.brand-layout>

    <x-table>
        <x-slot name="header">
            <tr class="bg-gray-700 text-white h-12 text-left">
                <th class="pl-5"></th>
                <th>Name</th>
                <th>Used by</th>
                <th class="pr-5 text-end"></th>
            </tr>
        </x-slot>

        <x-slot name="content">
            @foreach($brands as $brand)
                <tr class="odd:bg-white even:bg-gray-50 border-b last:rounded-br-xl h-10">
                    <td class="px-2">
                        <div class="flex items-center">
                            <label for="checked-checkbox" class="hidden"></label>
                            <input id="checked-checkbox" type="checkbox" value=""
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </td>
                    <td>
                        <a class="w-full h-full avenir-bold underline text-secondary hover:text-secondary-dark"
                           href="{{ route('warehouse.brands.show', $brand) }}">{{ $brand->name }}</a>
                    </td>
                    <td>
                        <ul class="list-disc pl-4">
                            <li>{{ $brand->products->count() }} Product(s)</li>
                        </ul>
                    </td>
                    <td class="text-end pr-2">
                        <x-dropdown align="right" width="36">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <i class="fa fa-ellipsis"></i>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('warehouse.brands.show', $brand)">
                                    <i class="fa fa-eye w-6"></i>Show
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('warehouse.brands.edit', $brand)">
                                    <i class="fa fa-pen w-6"></i>Edit
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('warehouse.brands.delete', $brand)">
                                    <i class="fa fa-trash w-6"></i>Delete
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </td>
                </tr>
            @endforeach
        </x-slot>
    </x-table>

</x-layouts.warehouse.brand-layout>

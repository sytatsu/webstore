@props([
    'checkbox' => false,
    'checked' => false,
])

<tr class="odd:bg-white even:bg-gray-50 border-b last:border-b-0 last:rounded-b-xl h-10">
    @if($checkbox)
        <x-table.partials.checkbox :checked="$checked" />
    @endif

    {{ $slot }}

    <td class="text-end pr-2">
        <x-dropdown align="right" width="36">
            <x-slot name="trigger">
                <button
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <i class="fa fa-ellipsis"></i>
                </button>
            </x-slot>

            <x-slot name="content">
                {{ $actions }}
            </x-slot>
        </x-dropdown>
    </td>
</tr>

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

        <x-actions.dropdown align="right" width="36">
            {{ $actions }}
        </x-actions.dropdown>
    </td>
</tr>

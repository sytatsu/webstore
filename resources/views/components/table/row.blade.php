@props([
    'checkbox' => false,
    'checked' => false,
])

<tr class="h-12 border-b last:border-b-0 last:rounded-b-xl h-10">
    @if($checkbox)
        <x-table.partials.checkbox :checked="$checked" />
    @endif

    {{ $slot }}

    <td class="text-end pr-2 px-4 w-0 whitespace-nowrap">

        <x-actions.dropdown align="right" width="36">
            {{ $actions }}
        </x-actions.dropdown>
    </td>
</tr>

@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white', 'active'])

<x-dropdown :align="$align" :width="$width" :contentClasses="$contentClasses">
    <x-slot name="trigger">
        <button
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
            <i class="fa fa-ellipsis"></i>
        </button>
    </x-slot>

    <x-slot name="content">
        {{ $slot }}
    </x-slot>
</x-dropdown>

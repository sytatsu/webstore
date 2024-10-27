<x-app-layout>
    <x-slot name="header">
        <x-layout-header title='Dashboard' />
    </x-slot>

    <x-container>
        <x-section class="hover:bg-gray-50">
            {{ __("You're logged in!") }}
        </x-section>
    </x-container>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <x-layout-header title='Profile Management'>
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex justify-between">
                <div class="flex justify-between">
                    <x-child-nav-link class="mr-2" :href="route('admin.management.profile.index')" :active="request()->routeIs('admin.management.profile.index')">
                        {{ __('List') }}
                    </x-child-nav-link>

                    <x-child-nav-link :href="route('admin.management.profile.create')" :active="request()->routeIs('admin.management.profile.create')">
                        <i class="fa fa-plus pr-1"></i> {{ __('Create') }}
                    </x-child-nav-link>
                </div>
            </div>
        </x-layout-header>
    </x-slot>

    <x-container>
        {{ $slot }}
    </x-container>
</x-app-layout>

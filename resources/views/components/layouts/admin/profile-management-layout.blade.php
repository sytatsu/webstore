<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between px-4">
            <div class="flex flex-row my-auto h-16 gap-10">
                <div class="hidden sm:flex justify-between gap-4 my-0 pl-4">
                    <x-nav-link class="mr-2" :href="route('admin.management.profile.index')"
                                      :active="request()->routeIs('admin.management.profile.index')">
                        {{ __('List') }}
                    </x-nav-link>

                    <x-nav-link :href="route('admin.management.profile.create')"
                                      :active="request()->routeIs('admin.management.profile.create')">
                        <i class="fa fa-plus pr-1"></i> {{ __('Create') }}
                    </x-nav-link>
                </div>
            </div>
        </div>
    </x-slot>

    <x-container>
        {{ $slot }}
    </x-container>
</x-app-layout>

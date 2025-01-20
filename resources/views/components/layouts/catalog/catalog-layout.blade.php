<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between px-4">
            <div class="flex flex-row my-auto h-16 gap-10">
                <div class="hidden sm:flex justify-between gap-4 my-0 pl-4">
                    <x-nav-link :href="route('catalog.products.list')" :active="request()->routeIs('catalog.products.*')">
                        {{ __('Products') }}
                    </x-nav-link>

                    <x-nav-vertical-line/>

                    <x-nav-link :href="route('catalog.brands.list')" :active="request()->routeIs('catalog.brands.*')">
                        {{ __('Brands') }}
                    </x-nav-link>

                    <x-nav-link :href="route('catalog.categories.list')" :active="request()->routeIs('catalog.categories.*')">
                        {{ __('Categories') }}
                    </x-nav-link>

                    <x-nav-link :href="route('catalog.variants.list')" :active="request()->routeIs('catalog.variants.*')">
                        {{ __('Variants') }}
                    </x-nav-link>
                </div>
            </div>

            @if(isset($actions))
                <div class="gap-2 flex my-auto">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </x-slot>

    <x-container>
        {{-- Debug purposes --}}
        @if ($errors->any() && env('APP_DEBUG'))
            @dump($errors)
        @endif

        {{ $slot }}
    </x-container>
</x-app-layout>

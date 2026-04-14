<div class="bg-background-light dark:bg-slate-800 shadow-md dark:shadow-slate-700">
    <div class="mx-auto xl:min-w-[80rem] md:max-w-[85rem] w-full px-4 md:px-6 lg:px-8 py-2 flex justify-between">
        <header class="sticky top-0 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap z-50 text-sm p-0 md:py-4" style="z-index: 60">
            <nav class="relative max-w-2xl w-full mx-2 py-2.5 md:flex md:items-center md:justify-between md:py-0 md:px-4 md:mx-auto">
                <div class="md:hidden">
                    <!-- Toggle Button -->
                    <button type="button"
                            class="md:hidden relative p-2 m-2 flex items-center font-medium text-[12px] rounded-lg text-gray-800 hover:bg-background-dark hover:inset-shadow-lg hover:text-white hover:bg-background-dark focus:outline-none focus:bg-background-dark focus:text-white focus:inset-shadow-lg disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-neutral-700 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
                            id="hs-header-base-collapse" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-header-base"
                            aria-label="Toggle navigation" data-hs-overlay="#hs-header-base">
                        <svg class="hs-collapse-open:hidden shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" x2="21" y1="6" y2="6"/>
                            <line x1="3" x2="21" y1="12" y2="12"/>
                            <line x1="3" x2="21" y1="18" y2="18"/>
                        </svg>
                        <svg class="hs-collapse-open:block hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                    <!-- End Toggle Button -->

                    <div class="hidden md:inline-block md:me-2">
                        <div class="w-px h-4 bg-gray-300 dark:bg-neutral-700"></div>
                    </div>
                </div>


                <!-- Collapse -->
                <div id="hs-header-base"
                     class="avenir-bold hs-overlay [--auto-close:md] hs-overlay-open:translate-x-0 -translate-x-full fixed top-0 start-0 z-60 transition-all duration-300 transform h-full max-w-xs w-full z-[60] bg-white md:bg-transparent border-e basis-full grow md:order-2 md:static md:block md:h-auto md:max-w-none md:w-auto md:border-e-transparent md:transition-none md:translate-x-0 md:z-40 md:basis-auto dark:bg-slate-900 dark:md:bg-transparent dark:border-e-gray-700 md:dark:border-e-transparent hidden "
                     role="dialog" tabindex="-1" aria-label="Sidebar"
                     data-hs-overlay-close-on-resize >
                    <div
                        class="overflow-hidden overflow-y-auto md:overflow-visible max-h-[75vh] md:max-h-none [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-slate-800 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                        <div class="py-2 md:py-0 px-2 md:px-0 flex flex-col md:flex-row md:items-center">
                            <div class="grow">
                                <div class="flex flex-col md:flex-row md:justify-center md:items-center gap-1 md:gap-6">
                                    <a class="px-1 m-3 md:ml-0 md:mr-3 md:my-0 flex items-center text-sm text-gray-800 border-b-2 border-transparent dark:text-neutral-200"
                                       href="{{ route('sytatsu.webstore.welcome')  }}"
                                    >
                                        <img src="{{ Vite::asset('resources/images/brands/no_background_text_only.webp') }}" alt="brand" width="70"
                                             class="md:hidden min-w-[70px]">
                                        <span class="hidden md:block min-w-[70px]">
                                            <img src="{{ Vite::asset('resources/images/brands/no_background_text_only.webp') }}" alt="brand" width="70">
                                        </span>
                                    </a>

                                    <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-6 border-b border-gray-100 dark:border-slate-700 last:border-b-0 last:md:border-b-1 last:mb-0">
                                        <a class="md:hidden px-1 m-3 md:m-0 flex items-center text-sm text-gray-800 border-b-2 border-transparent hover:border-secondary! dark:text-neutral-200 avenir-bold uppercase"
                                           href="{{ route('sytatsu.webstore.welcome') }}">
                                            <i class="fa fa-sm fa-house pr-2"></i> {{ __('Homepage') }}
                                        </a>

                                        {{-- Collections Dropdown --}}
                                        <div class="relative group">
                                            <button type="button" class="px-1 m-3 md:m-0 flex items-center text-sm text-gray-800 border-b-2 border-transparent hover:border-secondary! dark:text-neutral-200 avenir-bold uppercase text-nowrap">
                                                {{ __('Products') }} <i class="fa fa-chevron-down ml-2 hidden md:inline-block text-[10px]"></i>
                                            </button>

                                            <div class="md:absolute md:hidden md:group-hover:block bg-white dark:bg-slate-800 md:shadow-lg md:min-w-[200px] z-50 md:mt-0 md:pt-2 md:pb-2 pl-4 md:pl-0 md:top-full md:left-0">
                                                @foreach($collections as $groupId => $groupCollections)
                                                    @foreach($groupCollections as $collection)
                                                        @php
                                                            $isActive = $collection->defaultUrl && Request::is('collections/' . $collection->defaultUrl->slug . '*');
                                                        @endphp
                                                        @if($isActive)
                                                            <button type="button"
                                                                    wire:click="$dispatch('filtersReset')"
                                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-slate-700 avenir-bold uppercase !text-primary"
                                                            >
                                                                {{ $collection->translateAttribute('name') }}
                                                            </button>
                                                        @else
                                                            <a class="block px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-slate-700 avenir-bold uppercase text-nowrap"
                                                               href="{{ route('sytatsu.webstore.collection', $collection->defaultUrl?->slug ?? $collection->id) }}"
                                                            >
                                                                {{ $collection->translateAttribute('name') }}
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Services Dropdown --}}
                                        <div class="relative group">
                                            <button type="button" class="px-1 m-3 md:m-0 flex items-center text-sm text-gray-800 border-b-2 border-transparent hover:border-secondary! dark:text-neutral-200 avenir-bold uppercase text-nowrap">
                                                {{ __('Services') }} <i class="fa fa-chevron-down ml-2 hidden md:inline-block text-[10px]"></i>
                                            </button>

                                            <div class="md:absolute md:hidden md:group-hover:block bg-white dark:bg-slate-800 md:shadow-lg md:min-w-[200px] z-50 md:mt-0 md:pt-2 md:pb-2 pl-4 md:pl-0 md:top-full md:left-0">
                                                <a class="block px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-slate-700 avenir-bold uppercase text-nowrap {{ Request::routeIs('sytatsu.custom-print') ? 'text-primary' : '' }}"
                                                   href="{{ route('sytatsu.custom-print') }}">
                                                    {{ __('Custom Print') }}
                                                </a>

                                                <a class="block px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-slate-700 avenir-bold uppercase text-nowrap {{ Request::routeIs('sytatsu.maintenance-repair') ? 'text-primary' : '' }}"
                                                   href="{{ route('sytatsu.maintenance-repair') }}">
                                                    {{ __('Maintenance & Repairs') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Collapse -->

            </nav>
        </header>

        <div class="flex my-auto">
            <div class="flex flex-row-reverse gap-4 mx-2 md:px-4">
                <livewire:sytatsu.components.locale-switcher/>

                <button type="button"
                        class="dark:hidden flex hs-dark-mode font-medium text-gray-800 hover:text-gray-900 rounded-full focus:outline-none dark:text-neutral-200 dark:hover:text-neutral-300"
                        data-hs-theme-click-value="dark">
                          <span class="group inline-flex shrink-0 justify-center items-center size-9 my-auto">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                            </svg>
                          </span>
                </button>
                <button type="button"
                        class="dark:flex hidden hs-dark-mode font-medium text-gray-800 hover:text-gray-900 rounded-full focus:outline-none dark:text-neutral-200 dark:hover:text-neutral-300"
                        data-hs-theme-click-value="light">
                          <span class="group inline-flex shrink-0 justify-center items-center size-9 my-auto">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <circle cx="12" cy="12" r="4"></circle>
                              <path d="M12 2v2"></path>
                              <path d="M12 20v2"></path>
                              <path d="m4.93 4.93 1.41 1.41"></path>
                              <path d="m17.66 17.66 1.41 1.41"></path>
                              <path d="M2 12h2"></path>
                              <path d="M20 12h2"></path>
                              <path d="m6.34 17.66-1.41 1.41"></path>
                              <path d="m19.07 4.93-1.41 1.41"></path>
                            </svg>
                          </span>
                </button>

                <livewire:sytatsu.components.cart />
            </div>
        </div>
    </div>
</div>

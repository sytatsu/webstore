<header class="sticky-top top-0 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap z-50 md:ml-auto mr-auto text-sm p-0 md:p-6" style="z-index: 60">
    <nav class="relative max-w-2xl w-full mx-2 py-2.5 md:flex md:items-center md:justify-between md:py-0 md:px-4 md:mx-auto">

        <div class="md:hidden">
            <!-- Toggle Button -->
            <button type="button" class="md:hidden relative p-2 m-2 flex items-center font-medium text-[12px] rounded-lg text-gray-800 hover:bg-background-dark hover:inset-shadow-lg hover:text-white hover:bg-background-dark focus:outline-none focus:bg-background-dark focus:text-white focus:inset-shadow-lg disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-neutral-700 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" id="hs-header-base-collapse" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-header-base" aria-label="Toggle navigation" data-hs-overlay="#hs-header-base"  >
                <svg class="hs-collapse-open:hidden shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
                <svg class="hs-collapse-open:block hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <!-- End Toggle Button -->

            <div class="hidden md:inline-block md:me-2">
                <div class="w-px h-4 bg-gray-300 dark:bg-neutral-700"></div>
            </div>
        </div>


        <!-- Collapse -->
        <div id="hs-header-base" class="avenir-bold hs-overlay [--auto-close:md] hs-overlay-open:translate-x-0 -translate-x-full fixed top-0 start-0 transition-all duration-300 transform h-full max-w-xs w-full z-[60] bg-white md:bg-transparent border-e basis-full grow md:order-2 md:static md:block md:h-auto md:max-w-none md:w-auto md:border-e-transparent md:transition-none md:translate-x-0 md:z-40 md:basis-auto dark:bg-neutral-800 dark:md:bg-transparent dark:border-e-gray-700 md:dark:border-e-transparent hidden " role="dialog" tabindex="-1" aria-label="Sidebar" data-hs-overlay-close-on-resize  >
            <div class="overflow-hidden overflow-y-auto max-h-[75vh] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                <div class="py-2 md:py-0 px-2 md:px-0 flex flex-col md:flex-row md:items-center ">

                    <!-- End Offcanvas Header -->
                    <div class="grow">
                        <div class="flex flex-col md:flex-row md:justify-center md:items-center gap-0.5 md:gap-2">
                            <a class="px-1 m-3 md:m-0 flex items-center text-sm text-gray-800 border-b-2 border-transparent hover:!border-secondary dark:text-neutral-200
                                @if (Route::is('sytatsu.welcome')) !border-primary @endif"
                               href="{{ route('sytatsu.welcome')  }}"
                            ><svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden pr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                <i class="fa fa-house pb-1"></i>
                            </a>

                            <a class="px-1 m-3 md:m-0 flex items-center text-sm text-gray-800 border-b-2 border-transparent hover:!border-secondary dark:text-neutral-200
                               @if (Route::is('sytatsu.about')) !border-primary @endif"
                               href="{{ route('sytatsu.about')  }}"
                            ><svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden pr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ __('About us') }}
                            </a>

                            <a class="px-1 m-3 md:m-0 flex items-center text-sm text-gray-800 border-b-2 border-transparent hover:!border-secondary dark:text-neutral-200
                               @if (Route::is('sytatsu.contact')) !border-primary @endif"
                               href="{{ route('sytatsu.contact')  }}"
                            ><svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden pr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ __('Contact') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Collapse -->

    </nav>
</header>
<!-- ========== END HEADER ========== -->

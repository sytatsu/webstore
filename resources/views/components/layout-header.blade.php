<div class="flex flex-row justify-between px-4">
    <div class="flex flex-row my-auto h-16 gap-10">
        @if(!isset($showheader) ||  $showheader === true)
            <h2 class="font-semibold text-xl text-gray-800 leading-tight my-auto">
                {{ __($title ?? "N/A") }}
            </h2>
        @endif

        @if( isset($submenu) )
            {{ $submenu }}
        @endif
    </div>

    @if(isset($slot))
        <div class="flex my-auto">
            {{ $slot }}
        </div>
    @endif
</div>

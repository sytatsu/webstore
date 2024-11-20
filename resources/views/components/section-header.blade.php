<div class="flex flex-col w-full">
    <div class="flex flex-row justify-between py-2 text-slate-800 align-middle ">
        <div class="my-auto">
            <span class='text-2xl avenir-bold'>{{ $slot }}</span>
            <div class="mt-2 border-b-4 w-16 border-primary"></div>
        </div>

        @if (isset($actions))
            {{ $actions }}
        @endif
    </div>
</div>

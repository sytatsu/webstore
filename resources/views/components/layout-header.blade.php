<div class="flex flex-row justify-between px-4">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __($title ?? "N/A") }}
    </h2>

    @if(isset($slot))
        {{ $slot }}
    @endif
</div>

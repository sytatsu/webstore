@props(['message', 'subtext' => null, 'icon' => 'fa-paper-plane'])

<div class="flex flex-col md:flex-row gap-4 text-center p-4">
    <span class="my-auto text-4xl md:pr-4 text-gray-900 dark:text-white">
        <i class="fa {{ $icon }}"></i>
    </span>

    <div class="flex flex-col gap-4 text-gray-900 dark:text-white">
        <span>{{ $message }}</span>

        @if($subtext)
            <span class="text-xs text-gray-700 dark:text-neutral-400">{{ $subtext }}</span>
        @endif
    </div>
</div>

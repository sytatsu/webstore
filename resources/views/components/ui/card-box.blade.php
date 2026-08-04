@props(['title' => null])

<div {{ $attributes->merge(['class' => 'rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12']) }}>
    @if($title)
        <div class="divide-y divide-gray-200 dark:divide-gray-500">
            <h3 class="pb-4 text-lg font-bold text-black dark:text-white avenir-bold uppercase">
                {{ $title }}
            </h3>

            <div class="pt-4">
                {{ $slot }}
            </div>
        </div>
    @else
        {{ $slot }}
    @endif
</div>

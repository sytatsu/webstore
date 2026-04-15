@props(['title' => null])

<div {{ $attributes->merge(['class' => 'shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12']) }}>
    @if($title)
        <h3 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase">
            {{ $title }}
        </h3>

        <hr class="mb-4 border-gray-200 dark:border-gray-500">
    @endif

    {{ $slot }}
</div>

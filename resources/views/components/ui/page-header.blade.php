@props(['title', 'tag' => 'h2'])

<{{ $tag }} @class("text-2xl font-bold text-black dark:text-white mb-8 avenir-bold uppercase")>
    {{ __($title) }}
</{{ $tag }}>

<hr class="mb-8 border-gray-200 dark:border-gray-500">

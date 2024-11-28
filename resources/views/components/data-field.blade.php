@props(['title'])

<div {{ $attributes->onlyProps(['class'])->merge(['class' => 'flex flex-col pb-4 ']) }}>
    <span class='font-medium text-xs text-gray-500'>{{ $title }}</span>
    <div class="w-6 border-b my-1 border-gray-200"></div>
    <div class='text-md'>{{ $slot }}</div>
</div>

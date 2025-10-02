@props([
    'type' => 'primary',
    'onClick' => null,
    'route' => null,
])

@switch(strtolower($type))
    @case('primary')
        <a class="block mr-auto py-3 px-5 text-sm font-medium text-center text-white bg-primary-dark rounded-lg hover:bg-primary"
        @break

    @case('secondary')
        <a class="block mr-auto py-3 px-5 text-sm font-medium text-center text-white bg-secondary-dark rounded-lg hover:bg-secondary"
        @break

    @default
        <a class="block mr-auto py-3 px-5 text-sm font-medium text-center text-white dark:text-slate-800 bg-slate-800 dark:bg-slate-50 rounded-lg hover:bg-slate-900 dark:hover:bg-slate-100"
        @break
@endswitch

@if ($route)
   href="{{ route($route) }}"
@endif

@if ($onClick)
   wire:click.prevent="{{ $onClick }}"
   wire:loading.attr="disabled"
@endif
>
    {{ $slot }}
</a>

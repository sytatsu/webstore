@props([
    'label' => null,
    'id' => null,
])

<label @if($id) for="{{ $id }}" @endif class="flex items-center gap-2 cursor-pointer group">
    <input type="checkbox"
           @if($id) id="{{ $id }}" @endif
           {{ $attributes->merge(['class' => 'shrink-0 mt-0.5 size-4 border-gray-300 rounded text-primary focus:ring-primary dark:bg-slate-900 dark:border-gray-700 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-gray-800 transition-all duration-200']) }}>
    @if($label || $slot->isNotEmpty())
        <span class="text-sm text-gray-800 dark:text-gray-200 group-hover:text-primary transition-colors">
            {{ $label ?? $slot }}
        </span>
    @endif
</label>

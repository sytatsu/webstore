<x-ui.button.base classes="group flex justify-center text-slate-800 dark:text-white focus:outline-hidden transition disabled:opacity-50 disabled:pointer-events-none" buttonType="link" {{ $attributes }}>
    <div> {{-- Addition "div" so the underline animation works correctly --}}
        {{ $slot }}
        <span class="block max-w-0 group-hover:max-w-full transition-all duration-500 h-0.25 bg-slate-800 dark:bg-white"></span>
    </div>
</x-ui.button.base>

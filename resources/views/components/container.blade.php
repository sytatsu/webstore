<div class="py-12">
    <div class="fixed right-2 bottom-2">
        @foreach(app()->get(\App\Services\ToastService::class)->all() as $toast)
            @switch($toast['type'])
                @case('info')
                    <x-toast.info content="{{ $toast['content'] }}t"/>
                @break
                @case('success')
                    <x-toast.success content="{{ $toast['content'] }}"/>
                @break
                @case('danger')
                    <x-toast.danger content="{{ $toast['content'] }}"/>
                @break
                @case('warning')
                    <x-toast.warning content="{{ $toast['content'] }}" />
                @break
                @default
                    <x-toast.default content="{{ $toast['content'] }}"/>
            @endswitch
        @endforeach

    </div>
    <div {{ $attributes->merge(['class' => 'max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5']) }}>
        {{ $slot }}
    </div>
</div>

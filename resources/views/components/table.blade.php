<div {{ $attributes->onlyProps(['class'])->merge(['class' => 'w-full rounded']) }}>
    <table class="w-full whitespace-nowrap">
        @if(isset($header))
            <thead>
                {{ $header }}
            </thead>
        @endif
        <tbody>
            {{ $content }}
        </tbody>
    </table>
</div>

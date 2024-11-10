<div class="w-full border rounded">
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

<a  href="{{ $href }}" target="_blank" class="block duration-300 hover:scale-110"
    style="min-width: {{ $dimensions }}px; min-height: {{ $dimensions }}px">
    <img class="p-3 block dark:hidden" src="{{ $srcLight }}" alt="{{ $alt }}" width="{{ $dimensions }}px"/>
    <img class="p-3 hidden dark:block" src="{{ $srcDark }}" alt="{{ $alt }}" width="{{ $dimensions }}px"/>
</a>

<div class="flex flex-row rounded-lg">
    @foreach($supportedLocales as $supportedLocale => $supportedLocaleAttributes)
        @if($supportedLocale === $activeLocale['locale'])
            <div class="p-0.5 border-2 border border-primary rounded-full">
                <img src="{{ $activeLocale['image'] }}" alt="{{ $activeLocale['tooltip'] }}" width="24px" height="24px" />
            </div>
        @else
            <button type="button" wire:key="{{ $supportedLocale }}" wire:click.prevent="switchLocale('{{ $supportedLocale }}')" class="cursor-pointer flex flex-row justify-between p-0.5 border-2 border-transparent rounded-full">
                <img src="{{ $supportedLocaleAttributes['image'] }}" alt="{{ $supportedLocaleAttributes['tooltip'] }}" width="24px" height="24px" />
            </button>
        @endif
    @endforeach
</div>

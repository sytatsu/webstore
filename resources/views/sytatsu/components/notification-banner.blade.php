<div class="shadow-md dark:shadow-slate-700">
    @if($banner)
        @if($banner->banner_url)
            <a href="{{ $banner->banner_url }}" class="block hover:opacity-90 transition-opacity">
                <div class="bg-secondary px-4 py-2 text-white text-center text font-medium avenir-bold flex items-center justify-center gap-2">
                    @if($banner->banner_icon)
                        <x-filament::icon
                            :icon="$banner->banner_icon"
                            class="w-5 h-5"
                        />
                    @endif
                    {{ $banner->translate('banner_text') }}
                </div>
            </a>
        @else
            <div class="bg-secondary px-4 py-2 text-white text-center text font-medium avenir-bold flex items-center justify-center gap-2">
                @if($banner->banner_icon)
                    <x-filament::icon
                        :icon="$banner->banner_icon"
                        class="w-5 h-5"
                    />
                @endif
                {{ $banner->translate('banner_text') }}
            </div>
        @endif
    @endif
</div>

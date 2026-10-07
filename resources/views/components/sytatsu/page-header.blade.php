@props(['breadcrumbItems' => [], 'title'])

{{--
    The breadcrumb + title card first built for the product collection page
    (collection.blade.php) — its own full-width card, separate from whatever
    content card(s) follow it, rather than a bare title sitting on the page
    background. Pulled out here so the collections listing, custom print,
    maintenance and contact pages render the exact same header instead of
    each growing a slightly different one (see docs/bundles/README.md).
--}}
<div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-6 px-6 lg:px-12">
    @if(count($breadcrumbItems))
        <nav aria-label="{{ __('Breadcrumb') }}" class="mb-2 font-mono text-[10px] tracking-[.16em] uppercase text-gray-400 dark:text-neutral-500">
            @foreach($breadcrumbItems as $crumb)
                @if(!$loop->first) <span class="px-1">/</span> @endif
                @if($loop->last)
                    <span class="text-gray-600 dark:text-neutral-300">{{ $crumb['name'] }}</span>
                @else
                    <a href="{{ $crumb['item'] }}" class="hover:underline hover:text-primary">{{ $crumb['name'] }}</a>
                @endif
            @endforeach
        </nav>
    @endif

    <h1 class="text-2xl md:text-3xl avenir-bold text-black dark:text-white uppercase">
        {{ $title }}
    </h1>
</div>

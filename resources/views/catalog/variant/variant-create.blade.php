<x-layouts.catalog.variant-layout>
    <form method="post" action="{{ route('catalog.variants.store') }}" class="space-y-6">
        @csrf
        @method('put')

        @if(isset($variant))
            <input type="hidden" name="uuid" value="{{ $variant->uuid }}">
        @endif

        @include('catalog.variant.partials.variant-fields', [
           'title' => __('New Variant')
       ])
    </form>
</x-layouts.catalog.variant-layout>

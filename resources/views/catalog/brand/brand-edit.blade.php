<x-layouts.catalog.brand-layout>

    <form method="post" action="{{ route('catalog.brands.update', ['action' => $action]) }}" class="p-6 space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="uuid" value="{{ $brand->uuid }}">

        @include('catalog.brand.partials.brand-fields', [
            'brand' => $brand,
            'title' => sprintf('Edit: %s', $brand->name),
        ])
    </form>
</x-layouts.catalog.brand-layout>

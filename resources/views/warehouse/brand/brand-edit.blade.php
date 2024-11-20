<x-layouts.warehouse.brand-layout>

    <form method="post" action="{{ route('warehouse.brands.update', ['action' => $action]) }}" class="p-6 space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="uuid" value="{{ $brand->uuid }}">

        @include('warehouse.brand.partials.brand-fields', [
            'brand' => $brand,
            'title' => sprintf('Edit: %s', $brand->name),
        ])
    </form>
</x-layouts.warehouse.brand-layout>

<x-layouts.warehouse.brand-layout>

    <form method="post" action="{{ route('warehouse.brands.store') }}" class="space-y-6">
        @csrf
        @method('put')

        @include('warehouse.brand.partials.brand-fields', [
            'title' => 'New Brand',
        ])
    </form>
</x-layouts.warehouse.brand-layout>

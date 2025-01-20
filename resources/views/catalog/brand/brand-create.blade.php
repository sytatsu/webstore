<x-layouts.catalog.brand-layout>

    <form method="post" action="{{ route('catalog.brands.store') }}" class="space-y-6">
        @csrf
        @method('put')

        @include('catalog.brand.partials.brand-fields', [
            'title' => 'New Brand',
        ])
    </form>
</x-layouts.catalog.brand-layout>

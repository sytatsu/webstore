<x-layouts.warehouse.variant-layout>
    <form method="post" action="{{ route('warehouse.variants.update', ['action' => $action]) }}" class="space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="uuid" value="{{ $variant->uuid }}">

        @include('warehouse.variant.partials.variant-fields', [
            'variant' => $variant,
            'title'   => sprintf("Edit: %s", $variant->name),
        ])
    </form>
</x-layouts.warehouse.variant-layout>

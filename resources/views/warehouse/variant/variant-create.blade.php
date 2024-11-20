<x-layouts.warehouse.variant-layout>
    <form method="post" action="{{ route('warehouse.variants.store') }}" class="space-y-6">
        @csrf
        @method('put')

        @if(isset($variant))
            <input type="hidden" name="uuid" value="{{ $variant->uuid }}">
        @endif

        @include('warehouse.variant.partials.variant-fields', [
           'title' => __('New Variant')
       ])
    </form>
</x-layouts.warehouse.variant-layout>

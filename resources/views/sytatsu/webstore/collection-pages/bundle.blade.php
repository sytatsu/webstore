@extends('sytatsu.webstore.collection')

@section('content')
    <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-8 px-6 lg:p-12">
        <div id="bundle-panel-sentinel"></div>

        <livewire:sytatsu.components.bundle.bundle-panel
            :collection="$collection"
            :wire:key="'bundle-panel-'.$collection->id"
        />

        <hr class="my-8 border-gray-200 dark:border-gray-500">

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-6 md:gap-6 lg:gap-8 xl:gap-12">
            @foreach ($products as $product)
                <livewire:sytatsu.components.bundle.bundle-tile
                    :product="$product"
                    :wire:key="'bundle-tile-'.$product->id"
                />
            @endforeach
        </div>

        <livewire:sytatsu.components.bundle.bundle-product-modal />
    </div>
@endsection

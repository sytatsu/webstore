@props([
    'method',
    'action',
])

<form method="post" action="{{ $action }}" class="p-6 space-y-6">
    @csrf
    @method($method)

    @if(isset($variant))
        <input type="hidden" name="uuid" value="{{ $variant->uuid }}">
    @endif

    <div>
        <x-form.field.partials.label for="name" :value="__('Variant name')" />
        <x-form.field.partials.text :value="$variant?->name ?? ''" id="name" name="name" type="text" class="mt-1 block w-full" />
        <x-form.field.partials.error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-form.field.partials.label for="description" :value="__('Description')" />
        <x-form.field.partials.text :value="$variant?->description ?? ''" id="description" name="description" type="text" class="mt-1 block w-full" />
        <x-form.field.partials.error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Save') }}</x-primary-button>
    </div>
</form>

@props([
    'method',
    'action',
])

<form method="post" action="{{ $action }}" class="m-6 space-y-6">
    @csrf
    @method($method)

    <div>
        <x-input-label for="name" :value="__('Brand name')" />
        <x-text-input :value="$brand?->name ?? ''" id="name" name="name" type="text" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <x-text-input :value="$brand?->description ?? ''" id="description" name="description" type="text" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Save') }}</x-primary-button>
    </div>
</form>

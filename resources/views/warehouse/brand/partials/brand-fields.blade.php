@props([
    'title',
    'brand' => null,
])
<x-section width="w-full" innerClass="space-y-6">
    <x-section-header>
        {{ $title }}
    </x-section-header>

    <x-form.field.text name="name"
                       :label="__('Brand name')"
                       :value="$brand?->name"
                       class="mt-1 block w-full"/>


    <x-form.field.textarea name="Description"
                           :label="__('Description')"
                           :value="$brand?->description"
                           class="mt-1 block w-full"/>
</x-section>

<x-section width="w-full" class="p-6 space-y-6">
    <div class="flex items-center gap-4">
        <x-primary-button class="flex-grow justify-center">{{ __('Save') }}</x-primary-button>
    </div>
</x-section>

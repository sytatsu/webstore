<div>
    <x-form.field.partials.label :for="$attributes->get('name')" :value="$attributes->get('label')" />
    <input {{ $attributes->only('class')->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-grow']) }}
           id="{{ $attributes->get('name') }}" name="{{ $attributes->get('name') }}"
           value="{{ old($attributes->get('name')) ?? $attributes->get('value') ?? '' }}"
           type="number" min="0" step="1" />
    <x-form.field.partials.error :messages="$errors->get($attributes->get('name'))" class="mt-2" />
</div>

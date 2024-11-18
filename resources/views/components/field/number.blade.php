<div>
    <x-input-label :for="$attributes->get('name')" :value="$attributes->get('label')" />
    <div class="flex flex-row mt-1">
        <input {{ $attributes->only('class')->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-grow']) }}
               id="{{ $attributes->get('name') }}" name="{{ $attributes->get('name') }}"
               value="{{ old($attributes->get('name')) ?? $attributes->get('value') ?? '' }}"
               type="number" min="0" step="1" />
    </div>
    <x-input-error :messages="$errors->get($attributes->get('name'))" class="mt-2" />
</div>

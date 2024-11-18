<div>
    <x-input-label :for="$attributes->get('name')" :value="$attributes->get('label')" />
    <input {{ $attributes->only('class')->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}
           id="{{ $attributes->get('name') }}" name="{{ $attributes->get('name') }}"
           value="{{ old($attributes->get('name')) ?? $attributes->get('value') ?? '' }}"
           disabled="{{ $attributes->get('disabled') }}"
           type="text" />
    <x-input-error :messages="$errors->get($attributes->get('name'))" class="mt-2" />
</div>

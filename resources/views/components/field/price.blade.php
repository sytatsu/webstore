<div>
    <x-input-label :for="$attributes->get('name')" :value="$attributes->get('label')" />
    <div class="flex flex-row mt-1">
        <div class="bg-gray-200 border border-r-0 border-gray-300 text-gray-600 rounded-l-md shadow-sm px-3 text-center flex">
            <span class="my-auto avenir-bold">€</span>
        </div>
        <input {{ $attributes->only('class')->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-r-md shadow-sm flex-grow']) }}
               id="{{ $attributes->get('name') }}" name="{{ $attributes->get('name') }}"
               value="{{ old($attributes->get('name')) ?? $attributes->get('value') ?? '' }}"
               type="number" min="0.01" step="0.01" />
    </div>
    <x-input-error :messages="$errors->get($attributes->get('name'))" class="mt-2" />
</div>

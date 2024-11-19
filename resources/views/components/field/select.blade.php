<div class="{{ $attributes->get('outerClass') }}">
    <x-input-label :for="$attributes->get('name')" :value="$attributes->get('label')" />

    @if($attributes->get('multiple'))
        <x-select2-input {{ $attributes->only('class')->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}
                        :options="$attributes->get('options')"
                        :selected="old($attributes->get('name')) ?? $attributes->get('selected') ?? []"
                        :name="$attributes->get('name')"
                        :placeholder="$attributes->get('placeholder')"/>
    @else
        <x-select-input {{ $attributes->only('class')->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}
                        :options="$attributes->get('options')"
                        :selected="old($attributes->get('name')) ?? $attributes->get('selected') ?? []"
                        :name="$attributes->get('name')"
                        :placeholder="$attributes->get('placeholder')"/>
    @endif

    <x-input-error :messages="$errors->get($attributes->get('name'))" class="mt-2" />
</div>

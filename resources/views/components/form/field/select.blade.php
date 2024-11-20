<div class="{{ $attributes->get('outerClass') }}">
    <x-form.field.partials.label :for="$attributes->get('name')" :value="$attributes->get('label')" />

    @if($attributes->get('multiple'))
        <x-form.field.partials.select2 {{ $attributes->only('class')->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}
                        :options="$attributes->get('options')"
                        :selected="old($attributes->get('name')) ?? $attributes->get('selected') ?? []"
                        :name="$attributes->get('name')"
                        :placeholder="$attributes->get('placeholder')"
                        :position="$attributes->get('position')"/>
    @else
        <x-form.field.partials.select {{ $attributes->only('class')->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}
                        :options="$attributes->get('options')"
                        :selected="old($attributes->get('name')) ?? $attributes->get('selected') ?? []"
                        :name="$attributes->get('name')"
                        :placeholder="$attributes->get('placeholder')"/>
    @endif

    <x-form.field.partials.error :messages="$errors->get($attributes->get('name'))" class="mt-2" />
</div>

@props(['disabled' => false])

<select {{ $attributes->get('disabled') ? 'disabled' : '' }} {!! $attributes->exceptProps(['options', 'value', 'placeholder'])->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
    @if($attributes->get('placeholder'))
        <option>{{ __($attributes->get('placeholder')) }}</option>
    @endif

    @foreach($attributes->get('options') as $optionKey => $optionValue)
        <option value="{{ $optionKey }}" @if($optionKey === $attributes->get('value')) selected @endif>{{ $optionValue }}</option>
    @endforeach
</select>

@props(['disabled' => false])

<select {!! $attributes->onlyProps(['class'])->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}
    id="{{ $attributes->get('name') }}" name="{{ $attributes->get('name') }}"
    {{ $attributes->get('disabled') ? 'disabled' : '' }}>

    @if($attributes->get('placeholder'))
        <option>{{ __($attributes->get('placeholder')) }}</option>
    @endif

    @foreach($attributes->get('options') as $optionKey => $optionValue)
        <option value="{{ $optionKey }}" @if($optionKey === $attributes->get('selected')) selected @endif>{{ $optionValue }}</option>
    @endforeach
</select>

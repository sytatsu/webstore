@props(['field'])

@if($errors->has($field))
    <div {{ $attributes->merge(['class' => 'p-2 mt-4 text-xs font-medium text-center text-red-700 rounded bg-red-50']) }} role="alert">
        @foreach($errors->get($field) as $error)
            {{ __($error) }}
        @endforeach
    </div>
@endif

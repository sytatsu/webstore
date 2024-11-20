<tr {{ $attributes->merge(['class' => 'h-12 text-left border-b-2 avenir-bold']) }}>

{{--<tr {{ $attributes->merge(['class' => ' h-12 text-left--}}
{{--    rounded-t-xl text-left--}}
{{--    bg-gray-700 text-white--}}
{{--    ']) }}>--}}
    {{ $slot }}
</tr>

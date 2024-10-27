@props([
    'toastClasses' => 'border-grey-500',
    'iconClasses' => 'text-grey-500',
    'iconFace' => 'question-circle-o',
    'content' => 'Something went wrong with the content of this toast!'
])

@php($toastId = \Illuminate\Support\Str::uuid())

<div id="{{ $toastId }}" class="{{ $toastClasses }} bg-white shadow border rounded-lg py-4 px-4 my-2 mx-4 max-w-80 transition-all">
    <div class="flex justify-between">
        <div class="flex pr-3">
            <div class="{{ $iconClasses }} font-2xl pr-2">
                <i class="fa fa-lg fa-{{ $iconFace  }}"></i>
            </div>

            <p class="text-wrap">
                {{ $content }}
            </p>
        </div>
        <a class="flex text-gray-300 hover:text-gray-800 cursor-pointer" onclick="event.preventDefault(); document.getElementById('{{ $toastId }}').remove()">
            <i class="fa fa-times"></i>
        </a>
    </div>
</div>

@php
    $errorCode = trim($__env->yieldContent('code'));
    $errorTitle = trim($__env->yieldContent('title')) ?: __('Error');
    $errorMessage = trim($__env->yieldContent('message')) ?: $errorTitle;
@endphp
@component('layouts.sytatsu-layout', ['title' => ($errorCode ? $errorCode.' - ' : '').$errorTitle])
    <div class="mx-auto w-full max-w-[30rem] flex flex-col justify-center items-center">
        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 w-full text-center flex flex-col gap-6">
            <div class="divide-y divide-gray-200 dark:divide-gray-500">
                <h1 class="pt-4 text-3xl font-bold text-black dark:text-white avenir-bold uppercase">
                    {{ $errorMessage }}
                </h1>
            </div>

            <p class="text-slate-600 dark:text-gray-400">
                {{ __("The page you're looking for doesn't exist or is temporarily unavailable.") }}
            </p>

            <div class="mt-2">
                <x-ui.button.default.primary href="{{ route('sytatsu.webstore.welcome') }}">
                    {{ __('Back to home') }}
                </x-ui.button.default.primary>
            </div>
        </div>
    </div>
@endcomponent

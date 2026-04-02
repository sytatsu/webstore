<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full px-4 md:px-6 lg:px-8 flex flex-col justify-center items-center">
    <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 w-full max-w-2xl text-center flex flex-col gap-6">
        <span class="text-6xl" role="img">🥳</span>

        <h1 class="text-3xl font-bold text-black dark:text-white avenir-bold uppercase">
           {{ __('Order has been placed') }}
        </h1>

        <hr class="border-gray-200 dark:border-gray-500">

        <p class="font-medium text-lg text-black dark:text-white">
            {{ __('Your order reference number is') }} <strong class="underline">#{{ $order->reference }}</strong>
        </p>

        <p class="text-slate-600 dark:text-gray-400">
            {{ __('An email confirmation has been sent to the given e-mail, it may take a few minutes to arrive') }}
        </p>

        <div class="mt-4">
            <x-ui.button.default.primary href="{{ route('sytatsu.webstore.welcome') }}">
                {{ __('Go back to the store') }}
            </x-ui.button.default.primary>
        </div>
    </div>
</div>

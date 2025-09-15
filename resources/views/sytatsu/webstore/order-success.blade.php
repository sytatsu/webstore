<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full px-4 md:px-6 lg:px-8 py-12 lg:py-24 flex flex-col justify-center items-center gap-2">
        <h1 class="text-3xl font-extra bold sm:text-5xl text-center">
            <span class="block" role="img">🥳</span>
            <span class="block mt-1 text-slate-800 dark:text-white">
               Order has been placed
            </span>
        </h1>

        <p class="text-center font-medium sm:text-lg text-slate-800 dark:text-white">
            Your order reference number is <strong class="underline">#{{ $order->reference }}</strong>
        </p>

        <p class="text-center font-medium text-slate-800 dark:text-white">
            An email confirmation has been sent to the given e-mail, it may take a few minutes to arrive
        </p>

        <a class="pt-6 w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center rounded-lg text-slate-800 dark:text-white hover:underline focus:outline-hidden focus:underline transition disabled:opacity-50 disabled:pointer-events-none" href="{{ route('sytatsu.webstore.welcome') }}">
            Go back to the store
        </a>
</div>

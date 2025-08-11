<div class="flex flex-col gap-6 sticky-bottom bg-slate-50 dark:bg-slate-700 shadow-inner py-4">
    <p class="flex justify-center mx-auto my-0 text-secondary-light">
        {!! __('partials/footer.creator', ['creator' => '<a class="ml-1 underline text-secondary font-bold" href="https://stpronk.nl/">stpronk.nl</a>']) !!}
    </p>

    <p class="flex justify-center text-sm mx-auto my-0 text-slate-400 dark:text-slate-300">
        Copyright © {{ \Carbon\Carbon::now()->format('Y') }} - Sytatsu - {{ __('All rights reserved') }}
    </p>
</div>

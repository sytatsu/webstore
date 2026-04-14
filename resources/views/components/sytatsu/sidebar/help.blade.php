<div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-sm">
    <h3 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase text-sm">
        {{ __('Need Help?') }}
    </h3>

    <hr class="mb-4 border-gray-200 dark:border-gray-500">

    <p class="text-gray-600 dark:text-gray-400 mb-6">
        {{ __('If you have any questions about our products, services or your order, please contact our support.') }}
    </p>

    <a href="{{ route('sytatsu.contact') }}" class="inline-flex items-center gap-x-2 text-primary hover:text-primary-dark font-bold avenir-bold transition-colors">
        {{ __('Get in touch') }} <i class="fa fa-arrow-right"></i>
    </a>
</div>

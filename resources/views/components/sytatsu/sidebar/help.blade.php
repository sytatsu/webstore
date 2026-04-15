<x-ui.card-box title="{{ __('Need Help?') }}" class="text-sm">
    <p class="text-gray-600 dark:text-gray-400 mb-6">
        {{ __('If you have any questions about our products, services or your order, please contact our support.') }}
    </p>

    <a href="{{ route('sytatsu.contact') }}" class="inline-flex items-center gap-x-2 text-primary hover:text-primary-dark font-bold avenir-bold transition-colors">
        {{ __('Get in touch') }} <i class="fa fa-arrow-right"></i>
    </a>
</x-ui.card-box>

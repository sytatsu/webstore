<x-ui.card-box title="{{ __('Our Services') }}" class="text-sm">
    <p class="mb-6 text-gray-600 dark:text-neutral-400">
        {{ __('Whether you need a custom print or professional 3D printer maintenance, we’re here to help you get the best results.') }}
    </p>

    <div class="flex flex-col gap-3">
        <x-ui.button.default.primary class="w-full text-center" href="{{ route('sytatsu.custom-print') }}">
            {{ __('Custom Print') }} <i class="fa fa-print ml-2"></i>
        </x-ui.button.default.primary>

        <x-ui.button.default.primary class="w-full text-center" href="{{ route('sytatsu.maintenance-repair') }}">
            {{ __('Maintenance & Repairs') }} <i class="fa fa-wrench ml-2"></i>
        </x-ui.button.default.primary>
    </div>
</x-ui.card-box>

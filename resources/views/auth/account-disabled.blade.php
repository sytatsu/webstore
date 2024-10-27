<x-guest-layout>
    <p class="text-red-600 font-semibold py-2">
        Your account has been disabled
    </p>
    <div class="px-3 py-2 border rounded">
        <p><span class="font-semibold">Timestamp:</span> {{ $user->disabled_at->format('d-m-Y H:m') }}</p>
        <p><span class="font-semibold">Reason:</span> {{ $user->disabled_reason }}</p>
    </div>

    <p class="pt-2 pb-4">
        please contact support if you think this a mistake.
    </p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <x-secondary-button-link :href="route('logout')"
                     onclick="event.preventDefault();
                                               this.closest('form').submit();">
        <i class="fa fa-power-off"></i> {{ __('Log Out') }}
        </x-secondary-button-link>
    </form>
</x-guest-layout>

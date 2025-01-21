<x-app-layout>
    <x-container>
        <x-section >
            @include('profile.partials.update-profile-information-form')
        </x-section>

        <x-section >
            @include('profile.partials.update-password-form')
        </x-section>

        <x-section >
            @include('profile.partials.delete-user-form')
        </x-section>
    </x-container>
</x-app-layout>

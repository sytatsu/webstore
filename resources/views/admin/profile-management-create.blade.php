<x-layouts.admin.profile-management-layout>
    <x-section>
        <form method="POST" action="{{ route('admin.management.profile.store') }}">
            @method('PUT')
            @csrf
            <p class="text-sm pb-4">
                Creating a new user will give send them an e-mail to the given address with their temporary password to login to the application. With the first login, they will be required to set their own password.
            </p>

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ml-4">
                    {{ __('Create') }}
                </x-primary-button>
            </div>
        </form>
    </x-section>
</x-layouts.admin.profile-management-layout>

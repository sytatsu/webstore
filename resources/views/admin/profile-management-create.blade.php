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
                <x-form.field.partials.label for="name" :value="__('Name')" />
                <x-form.field.partials.text id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-form.field.partials.error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-form.field.partials.label for="email" :value="__('Email')" />
                <x-form.field.partials.text id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-form.field.partials.error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ml-4">
                    {{ __('Create') }}
                </x-primary-button>
            </div>
        </form>
    </x-section>
</x-layouts.admin.profile-management-layout>

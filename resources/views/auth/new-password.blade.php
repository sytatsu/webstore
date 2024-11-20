<x-app-layout>
    <x-container>
        <x-section>
            <form method="POST" action="{{ route('new-password-update.store') }}">
                @csrf

                <p>
                    Your password has been expired and/or you never set a new password. Please fill in a new password to get full access to the application.
                </p>

                <!-- Password -->
                <div class="mt-4">
                    <x-form.field.partials.label for="password" :value="__('Password')" />
                    <x-form.field.partials.text id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-form.field.partials.error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-form.field.partials.label for="password_confirmation" :value="__('Confirm Password')" />

                    <x-form.field.partials.text id="password_confirmation" class="block mt-1 w-full"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password" />

                    <x-form.field.partials.error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button>
                        {{ __('Reset Password') }}
                    </x-primary-button>
                </div>
            </form>
        </x-section>
    </x-container>
</x-app-layout>

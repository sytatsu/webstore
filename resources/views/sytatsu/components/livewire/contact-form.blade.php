<div class="flex flex-col">
    @if(!$hasBeenSend)
        <div class="grid gap-4 lg:gap-6">
            <x-ui.input.default.input label="Name" id="name" wire:model.blur="name" type="text" name="name" required/>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                <x-ui.input.default.input label="Email" id="email" wire:model.blur="email" type="email" name="email" required/>
                <x-ui.input.default.input label="Phone Number" id="phone" wire:model.blur="phone" type="text" name="phone"/>
            </div>

            <x-ui.input.default.textarea label="Message" id="details" wire:model.blur="details" type="text" name="details" rows="4"/>

            <x-ui.button.default.primary type="submit" wire:loading.attr="disabled" wire:click.prevent="send()">
                {{ __('Send') }} <i class="fa fa-paper-plane"></i>
            </x-ui.button.default.primary>

        </div>

        <div class="mt-3 text-center">
            <p class="text-sm text-gray-500 dark:text-neutral-500">
                {{ __('We\'ll get back to you as soon as possible.') }}
            </p>
        </div>
    @else
        <x-ui.form-success
            message="{{ __('Thank you for your message, we will do our best to get back as soon as possible!') }}"
            subtext="{{ __('A confirmation will be sent to your email as well.') }}"
        />
    @endif
</div>

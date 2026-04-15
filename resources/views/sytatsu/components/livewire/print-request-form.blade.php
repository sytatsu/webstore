<div class="flex flex-col">
    @if(!$hasBeenSend)
        <div class="grid gap-4 lg:gap-6">
            <div class="grid grid-cols-1 gap-4 lg:gap-6">
                <x-ui.input.default.input label="{{ __('Name') }}" id="name" wire:model.blur="name" type="text" name="name" required/>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                <x-ui.input.default.input label="{{ __('Email') }}" id="email" wire:model.blur="email" type="email" name="email" required/>
                <x-ui.input.default.input label="{{ __('Phone Number') }}" id="phone" wire:model.blur="phone" type="text" name="phone"/>
            </div>

            <x-ui.input.default.textarea label="{{ __('Details') }}" id="details" wire:model.blur="details" type="text" name="details" rows="6" placeholder="{{ __('Please describe your request in detail...') }}"/>

            <x-ui.button.default.primary type="submit" wire:loading.attr="disabled" wire:click.prevent="send()">
                {{ __('Submit custom print request') }} <i class="fa fa-paper-plane"></i>
            </x-ui.button.default.primary>
        </div>

        <div class="mt-3 text-center">
            <p class="text-sm text-gray-500 dark:text-neutral-500">
                {{ __('We\'ll get back to you as soon as possible with a quote or advice.') }}
            </p>
        </div>
    @else
        <x-ui.form-success
            message="{{ __('Thank you for your custom print request! We will get back to you as soon as possible.') }}"
            subtext="{{ __('A confirmation will be sent to your email as well.') }}"
        />
    @endif
</div>

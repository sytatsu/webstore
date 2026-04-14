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

            <div class="grid grid-cols-1 lg:gap-6">
                <x-ui.input.default.select label="{{ __('Service Type') }}" id="service_type" wire:model="service_type" name="service_type" required>
                    <option value="maintenance">{{ __('Maintenance') }}</option>
                    <option value="repair">{{ __('Repair') }}</option>
                </x-ui.input.default.select>
            </div>

            <x-ui.input.default.textarea label="{{ __('Details') }}" id="details" wire:model.blur="details" type="text" name="details" rows="6" placeholder="{{ __('Please describe your request in detail...') }}"/>

            <x-ui.button.default.primary type="submit" wire:loading.attr="disabled" wire:click.prevent="send()">
                {{ __('Submit Service Request') }} <i class="fa fa-paper-plane"></i>
            </x-ui.button.default.primary>
        </div>

        <div class="mt-3 text-center">
            <p class="text-sm text-gray-500 dark:text-neutral-500">
                {{ __('We\'ll get back to you as soon as possible with a quote or advice.') }}
            </p>
        </div>
    @else
        <div class="flex flex-col gap-4 text-center px-4 py-6 bg-white/10 shadow-md">
            <span class="my-auto text-4xl md:pr-4 text-gray-900 dark:text-white">
                <i class="fa fa-paper-plane"></i>
            </span>

            <div class="flex flex-col gap-4 text-gray-900 dark:text-white">
                <span class="text-gray-900 dark:text-white">
                    {{ __('Thank you for your service request! We will get back to you as soon as possible.') }}
                </span>

                <span class="text-xs text-gray-700 dark:text-white">
                    {{ __('A confirmation will be sent to your email as well.') }}
                </span>
            </div>
        </div>
    @endif
</div>

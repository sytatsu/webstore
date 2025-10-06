<div class="flex flex-col border rounded-xl p-4 sm:p-6 lg:p-8 bg-background-light dark:bg-slate-800 dark:border-neutral-700 shadow-lg dark:shadow-primary" >
    @if(!$hasBeenSend)
        <div class="grid gap-4 lg:gap-6">
            <x-ui.input.default.input label="Name" id="name" wire:model.blur="name" type="text" name="name" required/>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                <x-ui.input.default.input label="Email" id="email" wire:model.blur="email" type="email" name="email" required/>
                <x-ui.input.default.input label="Phone Number" id="phone" wire:model.blur="phone" type="text" name="phone"/>
            </div>

            <x-ui.input.default.textarea label="Message" id="message" wire:model.blur="details" type="text" name="message" rows="4"/>

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
        <div class="flex flex-col md:flex-row gap-4 text-center p-4">

            <span class="my-auto text-4xl md:pr-4 text-gray-900 dark:text-white">
                <i class="fa fa-paper-plane"></i>
            </span>

            <div class="flex flex-col gap-4 text-gray-900 dark:text-white">
                <span class="text-gray-900 dark:text-white">
                    {{ __('Thank you for your message, we will so our best to get back as soon as possible!') }}
                </span>

                <span class="text-xs text-gray-700 dark:text-white">
                    {{ __('A confirmation will be send to your email as well.') }}
                </span>
            </div>

        </div>
    @endif
</div>

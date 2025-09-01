<div class="flex flex-col border rounded-xl p-4 sm:p-6 lg:p-8 bg-background-light dark:bg-slate-800 dark:border-neutral-700 shadow-lg dark:shadow-primary" >
    @if(!$hasBeenSend)
        <div class="grid gap-4 lg:gap-6">
            <div>
                <label for="hs-fullname-contacts-1" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
                    <span class="text-red-500">*</span>
                    {{ __('Name') }}
                </label>
                <input type="text" wire:model.blur="name" name="hs-fullname-contacts-1" id="hs-fullname-contacts-1"
                       class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('name') ?: ' !border-red-500' }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                <div>
                    <label for="hs-email-contacts-1" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
                        <span class="text-red-500">*</span>
                        {{ __('Email') }}
                    </label>
                    <input type="email" name="hs-email-contacts-1" wire:model.blur="email" id="hs-email-contacts-1" autocomplete="email"
                           class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('email') ?: ' !border-red-500' }}">
                </div>

                <div>
                    <label for="hs-phone-number-1" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">{{ __('Phone Number') }}</label>
                    <input type="text" name="hs-phone-number-1" wire:model.blur="phone" id="hs-phone-number-1"
                           class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('phone') ?: ' !border-red-500' }}">
                </div>
            </div>

            <div>
                <label for="hs-about-contacts-1" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
                    <span class="text-red-500">*</span>
                    {{ __('Message') }}
                </label>
                <textarea id="hs-about-contacts-1" name="hs-about-contacts-1" wire:model.blur="details" rows="4"
                          class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('details') ?: ' !border-red-500' }}"></textarea>
            </div>
        </div>

        @if ($errors->any())
            <ul class="text-red-500 list-disc mt-5 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ ucfirst($error) }}</li>
                @endforeach
            </ul>
        @endif

        <div class="mt-6 grid">
            <button wire:loading.attr="disabled" wire:click.prevent="send()" type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary-dark text-white hover:bg-primary focus:outline-none focus:bg-primary disabled:opacity-50 disabled:pointer-events-none"
                    @if($errors->any()) disabled @endif
            >{{ __('Send') }} <i class="fa fa-paper-plane"></i></button>
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

<div>
    <div>
        <label for="hs-address-first-name" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            <span class="text-red-500">*</span>
            {{ __('First name') }}
        </label>
        <input type="text" name="hs-address-first-name" wire:model.blur="address.first_name" id="hs-address-first-name" autocomplete="first_name" required
               class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.first_name') ?: ' !border-red-500' }}">
    </div>

    <div>
        <label for="hs-address-last-name" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            <span class="text-red-500">*</span>
            {{ __('Last name') }}
        </label>
        <input type="text" name="hs-address-last-name" wire:model.blur="address.last_name" id="hs-address-last-name" autocomplete="last_name" required
               class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.last_name') ?: ' !border-red-500' }}">
    </div>

    <div class="col-span-2">
        <label for="hs-address-contact-email" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            <span class="text-red-500">*</span>
            {{ __('Email') }}
        </label>
        <input type="email" name="hs-address-contact-email" wire:model.blur="address.contact_email" id="hs-address-contact-email" autocomplete="email" required
               class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.contact_email') ?: ' !border-red-500' }}">
    </div>

    <div class="col-span-2">
        <label for="hs-address-contact-phone" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            {{ __('Phone Number') }}
        </label>
        <input type="text" name="hs-address-contact-phone" wire:model.blur="address.contact_phone" id="hs-address-contact-phone" autocomplete="phone"
               class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.contact_phone') ?: ' !border-red-500' }}">
    </div>

    <div>
        <label for="hs-address-line-one" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            <span class="text-red-500">*</span>
            {{ __('Street') }}
        </label>
        <input type="text" name="hs-address-line-one" wire:model.blur="address.line_one" id="hs-address-line-one" autocomplete="street" required
               class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.line_one') ?: ' !border-red-500' }}">
    </div>

    <div>
        <label for="hs-address-line-two" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            <span class="text-red-500">*</span>
            {{ __('House number') }}
        </label>
        <input type="text" name="hs-address-line-two" wire:model.blur="address.line_two" id="hs-address-line-two" autocomplete="house-number" required
               class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.line_two') ?: ' !border-red-500' }}">
    </div>

    <div>
        <label for="hs-address-line-three" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            {{ __('Addition') }}
        </label>
        <input type="text" name="hs-address-line-three" wire:model.blur="address.line_three" id="hs-address-line-three" autocomplete="addition"
               class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.line_three') ?: ' !border-red-500' }}">
    </div>

    <div>
        <label for="hs-address-city" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            <span class="text-red-500">*</span>
            {{ __('City') }}
        </label>
        <input type="text" name="hs-address-city" wire:model.blur="address.city" id="hs-address-city" autocomplete="city" required
               class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.city') ?: ' !border-red-500' }}">
    </div>

    <div>
        <label for="hs-address-postcode" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            <span class="text-red-500">*</span>
            {{ __('Postal Code') }}
        </label>
        <input type="text" name="hs-address-postcode" wire:model.blur="address.postcode" id="hs-address-postcode" autocomplete="postcode" required
               class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.postcode') ?: ' !border-red-500' }}">
    </div>

    <div>
        <label for="hs-address-country" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">
            <span class="text-red-500">*</span>
            {{ __('Country') }}
        </label>
        <select wire:model.defer="address.country_id" required
                class="py-3 px-4 block w-full bg-neutral-50 border-1 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600{{ !$errors->has('address.postcode') ?: ' !border-red-500' }}">
            <option selected disabled>{{ __('Choose your country') }}</option>
            @foreach ($this->countries as $country)
                <option value="{{ $country->id }}" wire:key="country_{{ $country->id }}">
                    {{ $country->native }}
                </option>
            @endforeach
        </select>
    </div>
</div>

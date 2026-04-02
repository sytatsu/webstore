<div class="grid grid-cols-2 gap-4">
    <div
        wire:loading.flex
        wire:target="saveAddress"
        style="display: none;"
        class="absolute inset-0 z-50 flex items-center justify-center bg-white/50 dark:bg-slate-800/50"
    >
        <svg class="w-12 h-12 text-primary animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- Personal info --}}
    <x-ui.input.default.input type="text" label="First Name" id="address.first_name" wire:model.blur="address.first_name" name="first_name" autocomplete="firstname" required/>
    <x-ui.input.default.input type="text" label="Last Name" id="address.last_name" wire:model.blur="address.last_name" name="last_name" autocomplete="lastname" required/>
    <x-ui.input.default.input parentClasses="col-span-2" type="text" label="Email" id="address.contact_email" wire:model.blur="address.contact_email" name="contact_email" autocomplete="email" required/>
    <x-ui.input.default.input parentClasses="col-span-2" type="text" label="Phone Number" id="address.contact_phone" wire:model.blur="address.contact_phone" name="contact_phone" autocomplete="phone"/>

    {{-- Address info --}}
    <x-ui.input.default.input type="text" label="Street" id="address.line_one" wire:model.blur="address.line_one" name="line_one" autocomplete="street" required/>
    <x-ui.input.default.input type="text" label="House number" id="address.line_two" wire:model.blur="address.line_two" name="line_two" autocomplete="house_number" required/>
    <x-ui.input.default.input type="text" label="Addition" id="address.line_three" wire:model.blur="address.line_three" name="line_three" autocomplete="addition"/>
    <x-ui.input.default.input type="text" label="City" id="address.city" wire:model.blur="address.city" name="city" autocomplete="city" required/>
    <x-ui.input.default.input type="text" label="Postal Code" id="address.postcode" wire:model.blur="address.postcode" name="postcode" autocomplete="postal_code" required/>

    <x-ui.input.default.select label="Country" id="address.country_id" wire:model.blur="address.country_id" name="country" autocomplete="country" required>
        <option value="">{{ __('Choose your country...') }}</option>
        @foreach ($this->countries as $country)
            <option value="{{ $country->id }}" wire:key="country_{{ $country->id }}">
                {{ $country->native }}
            </option>
        @endforeach
    </x-ui.input.default.select>
</div>

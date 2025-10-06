<div class="grid grid-cols-2 gap-4 ">
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

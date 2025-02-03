<form wire:submit="send()" class="flex flex-col justify-center gap-5 avenir-bold w-full px-8">
    <x-form.field.text :label="__('Name')" name="name" class="w-full"></x-form.field.text>
    <x-form.field.text :label="__('E-mail')" name="email" class="w-full"></x-form.field.text>
    <x-form.field.textarea :label="__('Message')" name="message" class="w-full"></x-form.field.textarea>

    <button wire:loading.attr="disabled" type="submit" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md avenir-bold text-xs text-white uppercase tracking-widest hover:bg-primary-dark focus:bg-primary-dark active:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:bg-primary-light disabled:cursor-not-allowed transition ease-in-out duration-150">
        {{ __('Send') }}
    </button>
</form>

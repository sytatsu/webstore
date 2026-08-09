<div>
    <x-ui.input.default.textarea
        label="{{ __('Notes for your order (optional)') }}"
        id="notes"
        wire:model.blur="notes"
        rows="3"
        placeholder="{{ __('Add any special instructions for your order...') }}"
    />
</div>

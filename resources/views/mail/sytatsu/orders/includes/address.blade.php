@props(['address', 'label'])

<div>
    <p style="margin: 0 0 6px 0; font-size: 12px; text-transform: uppercase; color: #64748b;">{{ $label }}</p>
    <p style="margin: 0; font-weight: bold; color: #1e293b;">{{ $address->full_name }}</p>
    @if($address->company_name)
        <p style="margin: 0; color: #1e293b;">{{ $address->company_name }}</p>
    @endif
    <p style="margin: 0; color: #1e293b;">{{ $address->line_one }}</p>
    @if($address->line_two)
        <p style="margin: 0; color: #1e293b;">{{ $address->line_two }}</p>
    @endif
    @if($address->line_three)
        <p style="margin: 0; color: #1e293b;">{{ $address->line_three }}</p>
    @endif
    <p style="margin: 0; color: #1e293b;">
        {{ $address->postcode }} {{ $address->city }}@if($address->state), {{ $address->state }}@endif
    </p>
    @if($address->country)
        <p style="margin: 0; color: #1e293b;">{{ $address->country->name }}</p>
    @endif
    @if($address->contact_phone)
        <p style="margin: 8px 0 0 0; color: #64748b; font-size: 14px;">{{ $address->contact_phone }}</p>
    @endif
</div>

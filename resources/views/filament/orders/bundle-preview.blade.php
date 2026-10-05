@props(['bundle'])

<div>
    <div style="display: flex; flex-wrap: wrap; gap: 8px 24px; font-size: 13px; margin-bottom: 16px;">
        <div><strong>{{ __('Bundle') }}:</strong> {{ $bundle['name'] ?? '—' }}</div>
        @if($bundle['tier'] ?? null)
            <div>
                <strong>{{ __('Tier matched') }}:</strong>
                {{ $bundle['tier']['min_quantity'] }}+ @ {{ \Illuminate\Support\Number::currency(($bundle['tier']['price'] ?? 0) / 100, in: 'EUR') }}
            </div>
        @endif
    </div>

    <div style="display: flex; flex-direction: column; gap: 8px;">
        @foreach($bundle['items'] ?? [] as $item)
            <div style="display: flex; align-items: center; gap: 10px;" wire:key="bundle-item-{{ $loop->index }}">
                <div style="width: 40px; height: 40px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(127, 127, 127, .35); flex-shrink: 0; background: #f3f4f6;">
                    @if($item['thumbnail'] ?? null)
                        <img src="{{ $item['thumbnail'] }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    @endif
                </div>
                <div style="font-size: 13px;">
                    <div style="font-weight: 600;">{{ $item['name'] ?? '—' }}</div>
                    <div style="color: #6b7280;">{{ __('Quantity') }}: {{ $item['quantity'] ?? 1 }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>

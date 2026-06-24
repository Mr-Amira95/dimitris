<div class="order-card"
     data-order-id="{{ $order->id }}"
     data-customer="{{ strtolower($order->customer?->name ?? '') }}"
     data-num="{{ strtolower($order->order_number ?? '') }}"
     onclick="openOrderDetail({{ $order->id }})">
    <div class="card-header">
        <span class="card-order-num">{{ $order->order_number }}</span>
        <span class="card-time">{{ $order->created_at->diffForHumans() }}</span>
    </div>
    <div class="card-customer">{{ $order->customer?->name }}</div>
    <div class="card-items">
        @php $count = $order->items->count(); @endphp
        @if($count === 0)
            No items
        @elseif($count <= 2)
            @foreach($order->items as $item)
                {{ $item->product?->name }} · {{ $item->filling?->name }} · {{ $item->grind?->name }} × {{ $item->qty }}<br>
            @endforeach
        @else
            {{ $order->items->take(2)->map(fn($i) => $i->product?->name)->join(', ') }}
            <span style="color:#4b5563"> +{{ $count - 2 }} more</span>
        @endif
    </div>
    @if($order->preferred_delivery_date)
    <div class="card-footer">
        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        {{ $order->preferred_delivery_date->format('M d') }}
        @if($order->preferred_delivery_time)
            · {{ \Carbon\Carbon::createFromTimeString($order->preferred_delivery_time)->format('g:i A') }}
        @endif
    </div>
    @endif
</div>

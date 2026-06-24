<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Filling;
use App\Models\Grind;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class KanbanController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'items.product', 'items.filling', 'items.grind', 'creator'])
            ->whereNull('archived_at')
            ->get()
            ->groupBy('status');

        $activeCount = Order::whereNull('archived_at')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->count();

        $archivedCount = Order::whereNotNull('archived_at')->count();

        $products = Product::active()->orderBy('name')->get(['id', 'name']);
        $fillings = Filling::active()->orderBy('name')->get(['id', 'name']);
        $grinds   = Grind::active()->orderBy('name')->get(['id', 'name']);

        return view('kanban.index', compact(
            'orders', 'activeCount', 'archivedCount',
            'products', 'fillings', 'grinds'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'             => 'required|exists:customers,id',
            'customer_phone_id'       => 'required|exists:customer_phones,id',
            'customer_address_id'     => 'required|exists:customer_addresses,id',
            'order_date'              => 'required|date',
            'preferred_delivery_date' => 'nullable|date',
            'preferred_delivery_time' => 'nullable|string|max:10',
            'internal_notes'          => 'nullable|string|max:2000',
            'items'                   => 'required|array|min:1',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.filling_id'      => 'required|exists:fillings,id',
            'items.*.grind_id'        => 'required|exists:grinds,id',
            'items.*.qty'             => 'required|integer|min:1|max:999',
        ]);

        $order = Order::create([
            'customer_id'             => $validated['customer_id'],
            'customer_phone_id'       => $validated['customer_phone_id'],
            'customer_address_id'     => $validated['customer_address_id'],
            'order_date'              => $validated['order_date'],
            'preferred_delivery_date' => $validated['preferred_delivery_date'] ?? null,
            'preferred_delivery_time' => $validated['preferred_delivery_time'] ?? null,
            'internal_notes'          => $validated['internal_notes'] ?? null,
            'status'                  => 'new',
            'created_by'              => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'filling_id' => $item['filling_id'],
                'grind_id'   => $item['grind_id'],
                'qty'        => $item['qty'],
            ]);
        }

        OrderLog::create([
            'order_id'  => $order->id,
            'user_id'   => auth()->id(),
            'action'    => 'created',
            'to_status' => 'new',
        ]);

        $order->refresh()->load(['customer', 'customerPhone', 'customerAddress', 'items.product', 'items.filling', 'items.grind', 'creator']);

        return response()->json(['success' => true, 'order' => $this->formatOrder($order)]);
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'customerPhone', 'customerAddress', 'items.product', 'items.filling', 'items.grind', 'creator', 'driver', 'logs.user']);

        return response()->json($this->formatOrder($order, true));
    }

    public function update(Request $request, Order $order)
    {
        $user        = auth()->user();
        $isAdmin     = $user->isAdmin();
        $role        = $user->role?->slug;
        $isProdPacking = $role === 'production' && $order->status === 'packing';
        $isSalesNew    = $role === 'sales' && $order->status === 'new' && $order->created_by === $user->id;

        if (!$isAdmin && !$isProdPacking && !$isSalesNew) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Production on Packing: items only
        if ($isProdPacking && !$isAdmin) {
            $validated = $request->validate([
                'items'              => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.filling_id' => 'required|exists:fillings,id',
                'items.*.grind_id'   => 'required|exists:grinds,id',
                'items.*.qty'        => 'required|integer|min:1|max:999',
            ]);
        } else {
            $validated = $request->validate([
                'customer_id'             => 'required|exists:customers,id',
                'customer_phone_id'       => 'required|exists:customer_phones,id',
                'customer_address_id'     => 'required|exists:customer_addresses,id',
                'order_date'              => 'required|date',
                'preferred_delivery_date' => 'nullable|date',
                'preferred_delivery_time' => 'nullable|string|max:10',
                'internal_notes'          => 'nullable|string|max:2000',
                'items'                   => 'required|array|min:1',
                'items.*.product_id'      => 'required|exists:products,id',
                'items.*.filling_id'      => 'required|exists:fillings,id',
                'items.*.grind_id'        => 'required|exists:grinds,id',
                'items.*.qty'             => 'required|integer|min:1|max:999',
            ]);

            $order->update([
                'customer_id'             => $validated['customer_id'],
                'customer_phone_id'       => $validated['customer_phone_id'],
                'customer_address_id'     => $validated['customer_address_id'],
                'order_date'              => $validated['order_date'],
                'preferred_delivery_date' => $validated['preferred_delivery_date'] ?? null,
                'preferred_delivery_time' => $validated['preferred_delivery_time'] ?? null,
                'internal_notes'          => $validated['internal_notes'] ?? null,
            ]);
        }

        $order->items()->delete();
        foreach ($validated['items'] as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'filling_id' => $item['filling_id'],
                'grind_id'   => $item['grind_id'],
                'qty'        => $item['qty'],
            ]);
        }

        OrderLog::create([
            'order_id' => $order->id,
            'user_id'  => auth()->id(),
            'action'   => 'edited',
        ]);

        $order->refresh()->load(['customer', 'customerPhone', 'customerAddress', 'items.product', 'items.filling', 'items.grind', 'creator', 'driver', 'logs.user']);

        return response()->json(['success' => true, 'order' => $this->formatOrder($order, true)]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,packing,dispatch,picked_up,delivered,cancelled',
        ]);

        $from = $order->status;
        $order->update(['status' => $validated['status']]);

        OrderLog::create([
            'order_id'    => $order->id,
            'user_id'     => auth()->id(),
            'action'      => 'status_changed',
            'from_status' => $from,
            'to_status'   => $validated['status'],
        ]);

        return response()->json(['success' => true]);
    }

    public function dispatch(Request $request, Order $order)
    {
        $validated = $request->validate([
            'driver_type'              => 'required|in:our,outsourced',
            'driver_user_id'           => 'required_if:driver_type,our|nullable|exists:users,id',
            'outsourced_driver_name'   => 'required_if:driver_type,outsourced|nullable|string|max:255',
            'outsourced_delivery_cost' => 'required_if:driver_type,outsourced|nullable|numeric|min:0',
        ]);

        $from = $order->status;
        $data = ['status' => 'dispatch', 'dispatched_at' => now()];

        if ($validated['driver_type'] === 'our') {
            $data['driver_user_id']         = $validated['driver_user_id'];
            $data['outsourced_driver_name'] = null;
        } else {
            $data['driver_user_id']            = null;
            $data['outsourced_driver_name']    = $validated['outsourced_driver_name'];
            $data['outsourced_delivery_cost']  = $validated['outsourced_delivery_cost'];
        }

        $order->update($data);

        OrderLog::create([
            'order_id'    => $order->id,
            'user_id'     => auth()->id(),
            'action'      => 'dispatched',
            'from_status' => $from,
            'to_status'   => 'dispatch',
        ]);

        return response()->json(['success' => true]);
    }

    public function addNote(Request $request, Order $order)
    {
        $validated = $request->validate(['note' => 'required|string|max:2000']);

        OrderLog::create([
            'order_id' => $order->id,
            'user_id'  => auth()->id(),
            'action'   => 'note_added',
            'note'     => $validated['note'],
        ]);

        return response()->json(['success' => true]);
    }

    public function archive(Order $order)
    {
        $order->update(['archived_at' => now()]);
        OrderLog::create(['order_id' => $order->id, 'user_id' => auth()->id(), 'action' => 'archived']);
        return response()->json(['success' => true]);
    }

    public function restore(Order $order)
    {
        $order->update(['archived_at' => null]);
        OrderLog::create(['order_id' => $order->id, 'user_id' => auth()->id(), 'action' => 'restored']);
        return response()->json(['success' => true]);
    }

    public function customers(Request $request)
    {
        $q = $request->query('q', '');

        $customers = Customer::where('is_active', true)
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'name']);

        return response()->json($customers);
    }

    public function customerPhones(Customer $customer)
    {
        return response()->json($customer->phones()->get(['id', 'phone', 'is_primary']));
    }

    public function customerAddresses(Customer $customer)
    {
        return response()->json($customer->addresses()->get(['id', 'address', 'is_primary']));
    }

    public function drivers()
    {
        $drivers = User::whereHas('role', fn($q) => $q->where('slug', 'driver'))
            ->where('is_active', true)
            ->get(['id', 'full_name']);

        return response()->json($drivers);
    }

    public function formatOrder(Order $order, bool $withLogs = false): array
    {
        $canViewCost = auth()->user()->isAdmin()
            || auth()->user()->hasPermission('view_delivery_cost');

        $data = [
            'id'                       => $order->id,
            'order_number'             => $order->order_number,
            'status'                   => $order->status,
            'status_label'             => $order->status_label,
            'customer_id'              => $order->customer_id,
            'customer_name'            => $order->customer?->name,
            'customer_phone_id'        => $order->customer_phone_id,
            'phone'                    => $order->customerPhone?->phone,
            'customer_address_id'      => $order->customer_address_id,
            'delivery_address'         => $order->customerAddress?->address,
            'order_date'               => $order->order_date?->format('Y-m-d'),
            'preferred_delivery_date'  => $order->preferred_delivery_date?->format('Y-m-d'),
            'preferred_delivery_time'  => $order->preferred_delivery_time,
            'internal_notes'           => $order->internal_notes,
            'created_by_id'            => $order->created_by,
            'created_by_name'          => $order->creator?->full_name,
            'created_at'               => $order->created_at?->toIso8601String(),
            'archived_at'              => $order->archived_at?->toIso8601String(),
            'is_archived'              => (bool) $order->archived_at,
            'dispatched_at'            => $order->dispatched_at?->toIso8601String(),
            'driver_user_id'           => $order->driver_user_id,
            'driver_user_name'         => $order->driver?->full_name,
            'outsourced_driver_name'   => $order->outsourced_driver_name,
            'outsourced_delivery_cost' => $canViewCost ? $order->outsourced_delivery_cost : null,
            'is_outsourced'            => $order->is_outsourced,
            'items'                    => $order->items->map(fn($i) => [
                'id'           => $i->id,
                'product_id'   => $i->product_id,
                'product_name' => $i->product?->name,
                'filling_id'   => $i->filling_id,
                'filling_name' => $i->filling?->name,
                'grind_id'     => $i->grind_id,
                'grind_name'   => $i->grind?->name,
                'qty'          => $i->qty,
            ]),
        ];

        if ($withLogs) {
            $data['logs'] = $order->logs->map(fn($l) => [
                'id'          => $l->id,
                'action'      => $l->action,
                'from_status' => $l->from_status ? (Order::STATUS_LABELS[$l->from_status] ?? $l->from_status) : null,
                'to_status'   => $l->to_status   ? (Order::STATUS_LABELS[$l->to_status]   ?? $l->to_status)   : null,
                'note'        => $l->note,
                'user_name'   => $l->user?->full_name,
                'created_at'  => $l->created_at?->toIso8601String(),
            ]);
        }

        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ArchivedController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('archive')) {
            abort(403);
        }

        $query = Order::with(['customer', 'creator'])
            ->withCount('items')
            ->whereNotNull('archived_at');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"))
                  ->orWhere('order_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('archived_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('archived_at', '<=', $dateTo);
        }

        $orders = $query->orderByDesc('archived_at')->get();

        return view('archived.index', compact('orders'));
    }
}

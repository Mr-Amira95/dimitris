<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('reports')) {
            abort(403);
        }

        $dateFrom = \Carbon\Carbon::parse($request->get('date_from', now()->startOfMonth()->format('Y-m-d')))->startOfDay();
        $dateTo   = \Carbon\Carbon::parse($request->get('date_to',   now()->format('Y-m-d')))->endOfDay();

        // ── Summary cards ─────────────────────────────────────────────────────
        $totalActive = Order::whereNull('archived_at')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->count();

        $delayed = Order::whereNull('archived_at')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->whereNotNull('preferred_delivery_date')
            ->where('preferred_delivery_date', '<', today())
            ->count();

        $totalArchived = Order::whereNotNull('archived_at')->count();

        $dfStr = $dateFrom->format('Y-m-d');
        $dtStr = $dateTo->format('Y-m-d');

        $deliveredInRange = Order::where('status', 'delivered')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dfStr, $dtStr])
            ->count();

        $cancelledInRange = Order::where('status', 'cancelled')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dfStr, $dtStr])
            ->count();

        $totalInRange = Order::whereBetween(DB::raw('DATE(created_at)'), [$dfStr, $dtStr])->count();

        // ── Orders by status (bar chart) — array of {status, total} objects ──
        $byStatus = Order::select('status', DB::raw('count(*) as total'))
            ->whereBetween(DB::raw('DATE(created_at)'), [$dfStr, $dtStr])
            ->groupBy('status')
            ->get()
            ->map(fn($r) => ['status' => $r->status, 'total' => (int) $r->total])
            ->values()
            ->toArray();

        // ── Orders per day (line chart) — array of {date, total} objects ─────
        $perDayRaw = Order::select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as total'))
            ->whereBetween(DB::raw('DATE(created_at)'), [$dfStr, $dtStr])
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('total', 'day')
            ->toArray();

        $perDay = [];
        $current = $dateFrom->copy()->startOfDay();
        $end     = $dateTo->copy()->startOfDay();
        while ($current <= $end) {
            $key      = $current->format('Y-m-d');
            $perDay[] = ['date' => $key, 'total' => (int) ($perDayRaw[$key] ?? 0)];
            $current->addDay();
        }

        // ── Outsourced deliveries ──────────────────────────────────────────────
        $canViewCost = auth()->user()->isAdmin()
            || auth()->user()->hasPermission('view_delivery_cost');

        $outsourcedOrders = $canViewCost
            ? Order::whereNotNull('outsourced_driver_name')
                ->whereBetween(DB::raw('DATE(created_at)'), [$dfStr, $dtStr])
                ->with('customer')
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $outsourcedTotal = $outsourcedOrders->sum('outsourced_delivery_cost');

        return view('reports.index', compact(
            'dateFrom', 'dateTo',
            'totalActive', 'delayed', 'totalArchived',
            'deliveredInRange', 'cancelledInRange', 'totalInRange',
            'byStatus', 'perDay',
            'outsourcedOrders', 'outsourcedTotal', 'canViewCost'
        ));
    }
}

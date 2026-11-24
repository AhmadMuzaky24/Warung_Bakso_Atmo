<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\CateringOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $perStatus = CateringOrder::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $count = fn (string $status): int => (int) ($perStatus[$status] ?? 0);

        return view('staff.dashboard', [
            'totalOrders' => (int) $perStatus->sum(),
            'submittedOrders' => $count(CateringOrder::STATUS_SUBMITTED),
            'processingOrders' => $count(CateringOrder::STATUS_PROCESSING),
            'completedOrders' => $count(CateringOrder::STATUS_COMPLETED),
            'rejectedOrders' => $count(CateringOrder::STATUS_REJECTED),
            'todayEvents' => CateringOrder::query()
                ->whereDate('event_date', now()->toDateString())
                ->whereIn('status', [CateringOrder::STATUS_SUBMITTED, CateringOrder::STATUS_PROCESSING])
                ->count(),
            'recentOrders' => CateringOrder::query()
                ->with('user')
                ->withCount('items')
                ->latest('id')
                ->limit(5)
                ->get(),
        ]);
    }
}

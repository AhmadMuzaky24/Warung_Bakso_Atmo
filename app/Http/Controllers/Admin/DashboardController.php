<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CateringOrder;
use App\Models\Menu;
use App\Models\User;
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

        $perPayment = CateringOrder::query()
            ->selectRaw('payment_status, count(*) as total')
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status');

        $countStatus = fn (string $status): int => (int) ($perStatus[$status] ?? 0);
        $countPayment = fn (string $paymentStatus): int => (int) ($perPayment[$paymentStatus] ?? 0);

        $today = now()->toDateString();
        $nextWeek = now()->addDays(7)->toDateString();

        $financials = CateringOrder::query()
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN status != ? THEN total_amount ELSE 0 END), 0) as active_revenue, '
                .'COALESCE(SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END), 0) as completed_revenue, '
                .'COALESCE(SUM(CASE WHEN payment_status = ? THEN dp_amount ELSE 0 END), 0) as verified_dp, '
                .'COALESCE(SUM(CASE WHEN payment_status = ? AND status != ? THEN dp_amount ELSE 0 END), 0) as pending_dp',
                [
                    CateringOrder::STATUS_REJECTED,
                    CateringOrder::STATUS_COMPLETED,
                    CateringOrder::PAYMENT_VERIFIED,
                    CateringOrder::PAYMENT_PENDING,
                    CateringOrder::STATUS_REJECTED,
                ]
            )
            ->first();

        return view('admin.dashboard', [
            'totalOrders' => (int) $perStatus->sum(),
            'submittedOrders' => $countStatus(CateringOrder::STATUS_SUBMITTED),
            'processingOrders' => $countStatus(CateringOrder::STATUS_PROCESSING),
            'completedOrders' => $countStatus(CateringOrder::STATUS_COMPLETED),
            'rejectedOrders' => $countStatus(CateringOrder::STATUS_REJECTED),
            'pendingPayments' => $countPayment(CateringOrder::PAYMENT_PENDING),
            'verifiedPayments' => $countPayment(CateringOrder::PAYMENT_VERIFIED),
            'rejectedPayments' => $countPayment(CateringOrder::PAYMENT_REJECTED),
            'activeRevenue' => (int) ($financials?->active_revenue ?? 0),
            'completedRevenue' => (int) ($financials?->completed_revenue ?? 0),
            'verifiedDp' => (int) ($financials?->verified_dp ?? 0),
            'pendingDp' => (int) ($financials?->pending_dp ?? 0),
            'todayEvents' => CateringOrder::query()
                ->whereDate('event_date', $today)
                ->whereIn('status', [CateringOrder::STATUS_SUBMITTED, CateringOrder::STATUS_PROCESSING])
                ->count(),
            'upcomingEvents' => CateringOrder::query()
                ->whereDate('event_date', '>=', $today)
                ->whereDate('event_date', '<=', $nextWeek)
                ->whereIn('status', [CateringOrder::STATUS_SUBMITTED, CateringOrder::STATUS_PROCESSING])
                ->count(),
            'activeUsersCount' => User::query()->where('is_active', true)->count(),
            'categoriesCount' => Category::query()->count(),
            'availableMenusCount' => Menu::query()->where('is_available', true)->count(),
            'totalMenusCount' => Menu::query()->count(),
            'pendingPaymentOrders' => CateringOrder::query()
                ->with('user')
                ->withCount('items')
                ->where('payment_status', CateringOrder::PAYMENT_PENDING)
                ->latest('id')
                ->limit(5)
                ->get(),
            'recentOrders' => CateringOrder::query()
                ->with('user')
                ->withCount('items')
                ->latest('id')
                ->limit(5)
                ->get(),
        ]);
    }
}

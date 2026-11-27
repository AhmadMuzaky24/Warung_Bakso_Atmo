<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringOrder;
use App\Services\CateringReportExportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'payment_status', 'start_date', 'end_date']);

        $baseQuery = CateringOrder::query()->filter($filters);

        $summaryRow = (clone $baseQuery)
            ->selectRaw(
                'COUNT(*) as total_orders, '
                .'COALESCE(SUM(total_amount), 0) as total_amount, '
                .'COALESCE(SUM(dp_amount), 0) as total_dp, '
                .'COALESCE(SUM(CASE WHEN payment_status = ? THEN dp_amount ELSE 0 END), 0) as verified_dp, '
                .'COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) as completed_orders, '
                .'COALESCE(SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END), 0) as verified_orders',
                [
                    CateringOrder::PAYMENT_VERIFIED,
                    CateringOrder::STATUS_COMPLETED,
                    CateringOrder::PAYMENT_VERIFIED,
                ]
            )
            ->first();

        $totalAmount = (int) ($summaryRow?->total_amount ?? 0);
        $totalDp = (int) ($summaryRow?->total_dp ?? 0);

        $orders = (clone $baseQuery)
            ->with(['user', 'items'])
            ->withCount('items')
            ->latest('event_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', [
            'orders' => $orders,
            'filters' => $filters,
            'summary' => [
                'total_orders' => (int) ($summaryRow?->total_orders ?? 0),
                'total_amount' => $totalAmount,
                'total_dp' => $totalDp,
                'verified_dp' => (int) ($summaryRow?->verified_dp ?? 0),
                'remaining_balance' => max(0, $totalAmount - $totalDp),
                'completed_orders' => (int) ($summaryRow?->completed_orders ?? 0),
                'verified_orders' => (int) ($summaryRow?->verified_orders ?? 0),
            ],
        ]);
    }

    public function export(Request $request, CateringReportExportService $exporter): BinaryFileResponse
    {
        $filters = $request->only(['search', 'status', 'payment_status', 'start_date', 'end_date']);

        $orders = CateringOrder::query()
            ->with(['user', 'items'])
            ->filter($filters)
            ->latest('event_date')
            ->latest('id')
            ->get();

        $file = $exporter->createXlsx($orders, $filters);

        return response()
            ->download($file['path'], $file['filename'], [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catering\UpdateCateringPaymentStatusRequest;
use App\Models\CateringOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CateringOrderController extends Controller
{
    /**
     * Daftar seluruh pesanan untuk verifikasi pembayaran, 10 per halaman.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'payment_status', 'start_date', 'end_date']);

        $orders = CateringOrder::query()
            ->with('user')
            ->withCount('items')
            ->filter($filters)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.catering.index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    public function show(CateringOrder $cateringOrder): View
    {
        $cateringOrder->load(['user', 'items']);

        return view('admin.catering.show', [
            'order' => $cateringOrder,
        ]);
    }

    /**
     * Admin hanya boleh mengubah `payment_status`. Request yang membawa `status`
     * ditolak (403) oleh UpdateCateringPaymentStatusRequest.
     */
    public function updatePaymentStatus(
        UpdateCateringPaymentStatusRequest $request,
        CateringOrder $cateringOrder
    ): RedirectResponse {
        $cateringOrder->update([
            'payment_status' => $request->validated('payment_status'),
        ]);

        return redirect()
            ->route('admin.catering.show', $cateringOrder)
            ->with('success', 'Status pembayaran '.$cateringOrder->booking_code.' diperbarui menjadi '.$cateringOrder->payment_status_label.'.');
    }
}

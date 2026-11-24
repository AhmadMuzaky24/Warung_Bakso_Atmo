<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catering\UpdateCateringOrderStatusRequest;
use App\Models\CateringOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CateringOrderController extends Controller
{
    /**
     * Daftar pesanan operasional: pencarian, filter status dan rentang tanggal, 10 per halaman.
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

        return view('staff.catering.index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    public function show(CateringOrder $cateringOrder): View
    {
        $cateringOrder->load(['user', 'items']);

        return view('staff.catering.show', [
            'order' => $cateringOrder,
        ]);
    }

    /**
     * Staff hanya boleh mengubah `status`. Request yang membawa `payment_status`
     * ditolak (403) oleh UpdateCateringOrderStatusRequest.
     */
    public function updateStatus(
        UpdateCateringOrderStatusRequest $request,
        CateringOrder $cateringOrder
    ): RedirectResponse {
        $cateringOrder->update([
            'status' => $request->validated('status'),
        ]);

        return redirect()
            ->route('staff.catering.show', $cateringOrder)
            ->with('success', 'Status pesanan '.$cateringOrder->booking_code.' diperbarui menjadi '.$cateringOrder->status_label.'.');
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catering\StoreCateringOrderRequest;
use App\Models\CateringOrder;
use App\Models\Menu;
use App\Services\CateringBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CateringOrderController extends Controller
{
    public function index(): View
    {
        $orders = auth()->user()->cateringOrders()->withCount('items')->latest()->paginate(10);

        return view('customer.catering.index', compact('orders'));
    }

    public function create(): View
    {
        $menus = Menu::where('is_available', true)->orderBy('name')->get(['id', 'name', 'price']);

        return view('customer.catering.create', compact('menus'));
    }

    public function store(StoreCateringOrderRequest $request, CateringBookingService $service): RedirectResponse
    {
        $order = $service->createOrder($request->user(), $request->validated());

        return redirect()->route('customer.catering.show', $order)
            ->with('success', 'Booking katering berhasil dibuat. Kode: '.$order->booking_code);
    }

    public function show(CateringOrder $cateringOrder, CateringBookingService $service): View
    {
        abort_unless($cateringOrder->user_id === auth()->id(), 403);

        $cateringOrder->load('items');

        return view('customer.catering.show', [
            'order' => $cateringOrder,
            'whatsappUrl' => $service->generateWhatsAppUrl($cateringOrder),
        ]);
    }
}

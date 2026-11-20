<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CateringOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $orders = $request->user()->cateringOrders()->latest()->get();

        return view('customer.dashboard', [
            'orders' => $orders,
            'totalOrders' => $orders->count(),
            'submittedOrders' => $orders->where('status', CateringOrder::STATUS_SUBMITTED)->count(),
            'completedOrders' => $orders->where('status', CateringOrder::STATUS_COMPLETED)->count(),
        ]);
    }
}

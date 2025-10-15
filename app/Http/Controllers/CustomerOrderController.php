<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Lunar\Models\Order;

class CustomerOrderController extends Controller
{
    /**
     * Display a listing of the authenticated user's orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order (only if it belongs to the authenticated user).
     */
    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Load relationships
        $order->load(['lines.purchasable', 'shippingAddress', 'billingAddress']);

        return view('orders.show', compact('order'));
    }
}

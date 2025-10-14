<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('order')->with('error', 'Your cart is empty. Please add items to your order.');
        }

        // Calculate cart totals
        $cartTotal = 0;
        $totalPages = 0;

        foreach ($cart as $item) {
            $cartTotal += floatval($item['total']) * intval($item['quantity']);

            // Calculate total pages from order groups
            if (isset($item['order_groups'])) {
                foreach ($item['order_groups'] as $group) {
                    $totalPages += intval($group['totalPages']) * intval($item['quantity']);
                }
            }
        }

        // Shipping methods
        $shippingMethods = [
            'standard' => ['name' => 'Standard Shipping (5-7 days)', 'price' => 5.99],
            'express' => ['name' => 'Express Shipping (2-3 days)', 'price' => 15.99],
            'overnight' => ['name' => 'Overnight Shipping', 'price' => 29.99],
        ];

        // Payment methods
        $paymentMethods = [
            'credit_card' => ['name' => 'Credit Card', 'icon' => '💳'],
            'paypal' => ['name' => 'PayPal', 'icon' => '🅿️'],
            'bank_transfer' => ['name' => 'Bank Transfer', 'icon' => '🏦'],
        ];

        return view('checkout.index', compact('cart', 'cartTotal', 'totalPages', 'shippingMethods', 'paymentMethods'));
    }

    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
            'shipping_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
            'billing_same_as_shipping' => 'nullable|boolean',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('order')->with('error', 'Your cart is empty.');
        }

        // Here you would typically:
        // 1. Create an order record in the database
        // 2. Process payment
        // 3. Send confirmation email
        // 4. Clear the cart

        // For now, we'll just store the order in session and redirect to confirmation
        $order = [
            'cart' => $cart,
            'shipping' => $validated,
            'order_date' => now()->toDateTimeString(),
            'order_number' => 'ORD-' . strtoupper(uniqid())
        ];

        session()->put('last_order', $order);
        session()->forget('cart');

        return redirect()->route('checkout.confirmation')->with('success', 'Order placed successfully!');
    }

    public function confirmation()
    {
        $order = session()->get('last_order');

        if (!$order) {
            return redirect()->route('order')->with('error', 'No order found.');
        }

        return view('checkout.confirmation', compact('order'));
    }

    public function getCart()
    {
        $cart = session()->get('cart', []);

        $cartTotal = 0;
        $totalPages = 0;
        $itemCount = 0;

        foreach ($cart as $item) {
            $cartTotal += floatval($item['total']) * intval($item['quantity']);
            $itemCount++;

            if (isset($item['order_groups'])) {
                foreach ($item['order_groups'] as $group) {
                    $totalPages += intval($group['totalPages']) * intval($item['quantity']);
                }
            }
        }

        return response()->json([
            'cart' => $cart,
            'cartTotal' => $cartTotal,
            'totalPages' => $totalPages,
            'itemCount' => $itemCount
        ]);
    }

    public function removeCartItem(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->item_id])) {
            unset($cart[$request->item_id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->route('order')->with('success', 'Cart cleared.');
    }
}

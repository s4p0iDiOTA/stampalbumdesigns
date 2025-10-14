<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'order_groups' => 'required|json',
            'quantity' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0'
        ]);

        // Decode the order groups from the new order page
        $orderGroups = json_decode($request->order_groups, true);

        // Get existing cart or initialize empty array
        $cart = session()->get('cart', []);

        // Create a unique cart item
        $cartItemId = uniqid('order_');

        $cart[$cartItemId] = [
            'order_groups' => $orderGroups,
            'quantity' => $request->quantity,
            'total' => $request->total,
            'created_at' => now()->toDateTimeString()
        ];

        session()->put('cart', $cart);

        // Redirect to checkout instead of cart index
        return redirect()->route('checkout.index')->with('success', 'Order added to cart!');
    }


    public function cart()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function updateCart(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully!');
    }

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully!');
    }
}

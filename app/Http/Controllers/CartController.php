<?php

namespace App\Http\Controllers;

//use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        //$products = Product::all();
        return view('products.index', compact('products'));
    }

    public function addToCart(Request $request)
    {
       // $product = Product::findOrFail($request->id);
        // dd($request->all());
/*          "_token" => "x39bE3OmeUYkEFRSqaJ2QQc8PJ9RcvrlMOe7qgPY"
         "search_value" => "Cuba"
         "p1" => "0.20"
         "quantity" => "1"
         "hidden_search_value" => "Cuba"
         "hidden_period_value" => "📅1971-1975 -- 📄52 pages" */

        $cart = session()->get('cart', []);
        
        // If the cart already has the product, increase the quantity
        if (isset($cart[1])) {
            $cart[1]['quantity'] += $request->quantity;
        } else {
            // If product not in cart, add it
            $cart[rand()] = [
                'search_value' => $request->search_value,
                'p1' => $request->p1,
                'quantity' => $request->quantity,
                'hidden_period_value' => $request->hidden_period_value
            ];
        }

       /* $cart[rand()] = [
            'name' => "asdasd",
            'price' => "123",
            'quantity' => $request->quantity
        ];*/

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
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

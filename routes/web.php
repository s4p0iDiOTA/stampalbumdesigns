<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;


/*Route::get('/test', function () {
    //return view('Data');
});*/

//Route::get('/test', [DataDumpController::class, 'dumpData']);

//cart
Route::get('/products', [CartController::class, 'index'])->name('products.index');
Route::post('/cart', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [CartController::class, 'cart'])->name('cart.index');
Route::patch('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');

// Checkout routes
Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [App\Http\Controllers\CheckoutController::class, 'processCheckout'])->name('checkout.process');
Route::get('/checkout/confirmation', [App\Http\Controllers\CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::get('/api/cart', [App\Http\Controllers\CheckoutController::class, 'getCart'])->name('api.cart');
Route::delete('/checkout/remove/{item_id}', [App\Http\Controllers\CheckoutController::class, 'removeCartItem'])->name('checkout.remove');
Route::get('/checkout/clear', [App\Http\Controllers\CheckoutController::class, 'clearCart'])->name('checkout.clear');

// Shipping rate calculation routes
Route::post('/api/shipping/rates', [App\Http\Controllers\ShippingRateController::class, 'getRates'])->name('shipping.rates');
Route::get('/api/shipping/breakdown', [App\Http\Controllers\ShippingRateController::class, 'getBreakdown'])->name('shipping.breakdown');
Route::get('/api/shipping/test', [App\Http\Controllers\ShippingRateController::class, 'testConnection'])->name('shipping.test');

// Paper type routes
Route::get('/api/paper-types', [App\Http\Controllers\PaperTypeController::class, 'index'])->name('paper-types.index');
Route::get('/api/paper-types/{id}', [App\Http\Controllers\PaperTypeController::class, 'show'])->name('paper-types.show');
Route::post('/api/paper-types/calculate', [App\Http\Controllers\PaperTypeController::class, 'calculate'])->name('paper-types.calculate');
Route::get('/api/paper-types/album/{albumType}', [App\Http\Controllers\PaperTypeController::class, 'forAlbum'])->name('paper-types.for-album');

Route::get('/search-country', [CountryController::class, 'search']);
Route::get('/countries', [CountryController::class, 'listCountryNames']);

//top menu routes
Route::get('/', function () {
    return view('home');
});

Route::get('/home2', function () {
    return view('home2');
});

Route::get('/order', function () {
    $cart = session()->get('cart', []);
    return view('order', compact('cart'));
})->name('order');

/*
Route::get('/contact', function () {
    return view('contact');
});*/
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

//

//admin - only for users with 'admin' role
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('dashboard');

// Customer orders - for authenticated users to see their own orders
Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [App\Http\Controllers\CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{order}', [App\Http\Controllers\CustomerOrderController::class, 'show'])->name('orders.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Quick logout via GET (for development convenience)
// WARNING: In production, use POST /logout with CSRF token for security
Route::get('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/')->with('status', 'You have been logged out.');
})->middleware('auth')->name('logout.get');

require __DIR__ . '/auth.php';

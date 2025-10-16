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
            // Safety check for cart structure
            if (isset($item['total']) && isset($item['quantity'])) {
                $cartTotal += floatval($item['total']) * intval($item['quantity']);

                // Calculate total pages from order groups
                if (isset($item['order_groups'])) {
                    foreach ($item['order_groups'] as $group) {
                        $totalPages += intval($group['totalPages']) * intval($item['quantity']);
                    }
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

        // Calculate totals
        $cartTotal = 0;
        $totalPages = 0;
        foreach ($cart as $item) {
            if (isset($item['total']) && isset($item['quantity'])) {
                $cartTotal += floatval($item['total']) * intval($item['quantity']);
                if (isset($item['order_groups'])) {
                    foreach ($item['order_groups'] as $group) {
                        $totalPages += intval($group['totalPages']) * intval($item['quantity']);
                    }
                }
            }
        }

        // Get shipping cost
        $shippingCost = match($validated['shipping_method']) {
            'express' => 15.99,
            'overnight' => 29.99,
            default => 5.99,
        };

        try {
            // Create Lunar Order
            $currency = \Lunar\Models\Currency::getDefault();
            $channel = \Lunar\Models\Channel::getDefault();

            // Get or create test product variant for order lines
            $variant = $this->getOrCreateTestProductVariant($currency);

            // Look up country by name
            $country = \Lunar\Models\Country::where('name', 'like', '%' . $validated['shipping_country'] . '%')->first();

            // Create empty tax breakdown
            $taxBreakdown = new \Lunar\Base\ValueObjects\Cart\TaxBreakdown();
            $taxBreakdown->amounts = collect([]);

            $lunarOrder = \Lunar\Models\Order::create([
                'channel_id' => $channel->id,
                'user_id' => auth()->id(), // Link order to authenticated user
                'status' => 'awaiting-payment',
                'reference' => 'ORD-' . strtoupper(uniqid()),
                'customer_reference' => null,
                'sub_total' => (int)($cartTotal * 100), // Convert to cents
                'discount_total' => 0,
                'discount_breakdown' => null,
                'shipping_total' => (int)($shippingCost * 100),
                'shipping_breakdown' => null,
                'tax_total' => 0,
                'tax_breakdown' => $taxBreakdown,
                'total' => (int)(($cartTotal + $shippingCost) * 100),
                'notes' => 'Order from legacy cart system',
                'currency_code' => $currency->code,
                'compare_currency_code' => $currency->code,
                'exchange_rate' => 1,
                'placed_at' => now(),
                'meta' => [
                    'payment_method' => $validated['payment_method'],
                    'shipping_method' => $validated['shipping_method'],
                ],
            ]);

            // Create shipping address
            \Lunar\Models\OrderAddress::create([
                'order_id' => $lunarOrder->id,
                'country_id' => $country?->id,
                'title' => null,
                'first_name' => explode(' ', $validated['shipping_name'])[0] ?? $validated['shipping_name'],
                'last_name' => explode(' ', $validated['shipping_name'])[1] ?? '',
                'company_name' => null,
                'line_one' => $validated['shipping_address'],
                'line_two' => null,
                'line_three' => null,
                'city' => $validated['shipping_city'],
                'state' => $validated['shipping_state'],
                'postcode' => $validated['shipping_zip'],
                'delivery_instructions' => null,
                'contact_email' => auth()->user()->email ?? 'guest@example.com',
                'contact_phone' => null,
                'type' => 'shipping',
                'shipping_option' => $validated['shipping_method'],
                'meta' => [],
            ]);

            // Create billing address (same as shipping for now)
            \Lunar\Models\OrderAddress::create([
                'order_id' => $lunarOrder->id,
                'country_id' => $country?->id,
                'title' => null,
                'first_name' => explode(' ', $validated['shipping_name'])[0] ?? $validated['shipping_name'],
                'last_name' => explode(' ', $validated['shipping_name'])[1] ?? '',
                'company_name' => null,
                'line_one' => $validated['shipping_address'],
                'line_two' => null,
                'line_three' => null,
                'city' => $validated['shipping_city'],
                'state' => $validated['shipping_state'],
                'postcode' => $validated['shipping_zip'],
                'delivery_instructions' => null,
                'contact_email' => auth()->user()->email ?? 'guest@example.com',
                'contact_phone' => null,
                'type' => 'billing',
                'shipping_option' => null,
                'meta' => [],
            ]);

            // Create order lines from cart items
            foreach ($cart as $itemId => $item) {
                if (isset($item['total']) && isset($item['quantity'])) {
                    \Lunar\Models\OrderLine::create([
                        'order_id' => $lunarOrder->id,
                        'purchasable_type' => \Lunar\Models\ProductVariant::class,
                        'purchasable_id' => $variant->id,
                        'type' => 'physical',
                        'description' => 'Stamp Album Pages - ' . ($item['order_groups'][0]['country'] ?? 'Custom Order'),
                        'option' => null,
                        'identifier' => $variant->sku,
                        'unit_price' => (int)($item['total'] * 100),
                        'unit_quantity' => $item['quantity'],
                        'quantity' => $item['quantity'],
                        'sub_total' => (int)($item['total'] * $item['quantity'] * 100),
                        'discount_total' => 0,
                        'tax_total' => 0,
                        'total' => (int)($item['total'] * $item['quantity'] * 100),
                        'notes' => null,
                        'tax_breakdown' => $taxBreakdown,
                        'meta' => [
                            'order_groups' => $item['order_groups'] ?? [],
                            'total_pages' => array_sum(array_column($item['order_groups'] ?? [], 'totalPages')),
                        ],
                    ]);
                }
            }

            // Store order info for confirmation page
            $order = [
                'cart' => $cart,
                'shipping' => $validated,
                'order_date' => now()->toDateTimeString(),
                'order_number' => $lunarOrder->reference,
                'lunar_order_id' => $lunarOrder->id,
            ];

            session()->put('last_order', $order);
            session()->forget('cart');

            return redirect()->route('checkout.confirmation')->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            \Log::error('Order creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create order. Please try again.');
        }
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
            // Safety check for cart structure
            if (isset($item['total']) && isset($item['quantity'])) {
                $cartTotal += floatval($item['total']) * intval($item['quantity']);
                $itemCount++;

                if (isset($item['order_groups'])) {
                    foreach ($item['order_groups'] as $group) {
                        $totalPages += intval($group['totalPages']) * intval($item['quantity']);
                    }
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

    /**
     * Get or create a test product variant for order lines.
     * This is a temporary solution until proper products are set up in Phase 2.
     */
    private function getOrCreateTestProductVariant($currency)
    {
        // Check if test product already exists
        $productType = \Lunar\Models\ProductType::firstOrCreate(
            ['name' => 'Stamp Album Pages'],
            ['name' => 'Stamp Album Pages']
        );

        $product = \Lunar\Models\Product::where('product_type_id', $productType->id)->first();

        if (!$product) {
            $product = new \Lunar\Models\Product([
                'product_type_id' => $productType->id,
                'status' => 'published',
                'brand_id' => null,
            ]);
            $product->attribute_data = collect([
                'name' => new \Lunar\FieldTypes\Text('Stamp Album Pages'),
            ]);
            $product->save();
        }

        // Get or create tax class
        $taxClass = \Lunar\Models\TaxClass::firstOrCreate(
            ['name' => 'Default'],
            ['name' => 'Default', 'default' => true]
        );

        // Get or create variant
        $variant = \Lunar\Models\ProductVariant::firstOrCreate(
            ['product_id' => $product->id],
            [
                'product_id' => $product->id,
                'tax_class_id' => $taxClass->id,
                'sku' => 'STAMP-001',
                'stock' => 9999,
                'shippable' => true,
                'purchasable' => 'always',
            ]
        );

        // Create price if it doesn't exist
        if ($variant->prices()->count() === 0) {
            $variant->prices()->create([
                'price' => 1000, // $10.00 in cents
                'currency_id' => $currency->id,
                'min_quantity' => 1,
            ]);
        }

        return $variant;
    }
}

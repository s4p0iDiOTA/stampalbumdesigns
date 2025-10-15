<x-layout>
    <style>
        .checkout-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .checkout-step {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .step-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .step-number {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .step-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .shipping-method, .payment-method {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .shipping-method:hover, .payment-method:hover {
            border-color: #3b82f6;
            background: #f8fafc;
        }

        .shipping-method input:checked + label,
        .payment-method input:checked + label {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .order-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .order-summary-box {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            position: sticky;
            top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-row.total {
            border-bottom: none;
            border-top: 2px solid #3b82f6;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e293b;
            margin-top: 0.5rem;
            padding-top: 1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }
    </style>

    <main class="container checkout-container" x-data="checkoutData()">
        <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 2rem;">Checkout</h1>

        @if(session('success'))
            <div style="background: #d1fae5; border: 2px solid #10b981; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid">
            <!-- Left Column - Checkout Steps -->
            <div style="grid-column: span 8;">
                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf

                    <!-- Step 1: Review Order -->
                    <div class="checkout-step">
                        <div class="step-header">
                            <div class="step-number">1</div>
                            <h2 class="step-title">Review Your Order</h2>
                        </div>

                        @foreach($cart as $itemId => $item)
                            @if(isset($item['total']) && isset($item['quantity']))
                                <div class="order-item">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                        <div style="font-weight: 600; color: #1e293b; font-size: 1.1rem;">
                                            Order #{{ substr($itemId, -8) }}
                                        </div>
                                        <div style="font-weight: 700; color: #059669; font-size: 1.1rem;">
                                            ${{ number_format($item['total'], 2) }}
                                        </div>
                                    </div>

                                    @if(isset($item['order_groups']))
                                        @foreach($item['order_groups'] as $group)
                                            <div style="padding: 0.5rem 0; border-top: 1px solid #e2e8f0; margin-top: 0.5rem;">
                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                    <div>
                                                        <strong>{{ $group['country'] }}</strong>
                                                        <span style="color: #64748b; font-size: 0.9rem; margin-left: 0.5rem;">
                                                            ({{ $group['actualYearRange'] }})
                                                        </span>
                                                    </div>
                                                    <div style="color: #64748b; font-size: 0.9rem;">
                                                        {{ $group['totalPages'] }} pages × ${{ number_format($group['paperType'], 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e2e8f0;">
                                        <span style="color: #64748b; font-size: 0.9rem;">Quantity: {{ $item['quantity'] }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Step 2: Shipping Method -->
                    <div class="checkout-step">
                        <div class="step-header">
                            <div class="step-number">2</div>
                            <h2 class="step-title">Select Shipping Method</h2>
                        </div>

                        @foreach($shippingMethods as $key => $method)
                            <label class="shipping-method">
                                <input type="radio" name="shipping_method" value="{{ $key }}"
                                       x-model="shippingMethod"
                                       :data-price="{{ $method['price'] }}"
                                       required>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-left: 2rem; margin-top: -1.5rem;">
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b;">{{ $method['name'] }}</div>
                                    </div>
                                    <div style="font-weight: 700; color: #3b82f6;">
                                        ${{ number_format($method['price'], 2) }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Step 3: Shipping Address -->
                    <div class="checkout-step">
                        <div class="step-header">
                            <div class="step-number">3</div>
                            <h2 class="step-title">Shipping Address</h2>
                        </div>

                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="shipping_name">Full Name *</label>
                                <input type="text" id="shipping_name" name="shipping_name" required>
                                @error('shipping_name')
                                    <small style="color: #ef4444;">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group full-width">
                                <label for="shipping_address">Street Address *</label>
                                <input type="text" id="shipping_address" name="shipping_address" required>
                                @error('shipping_address')
                                    <small style="color: #ef4444;">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="shipping_city">City *</label>
                                <input type="text" id="shipping_city" name="shipping_city" required>
                                @error('shipping_city')
                                    <small style="color: #ef4444;">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="shipping_state">State/Province *</label>
                                <input type="text" id="shipping_state" name="shipping_state" required>
                                @error('shipping_state')
                                    <small style="color: #ef4444;">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="shipping_zip">ZIP/Postal Code *</label>
                                <input type="text" id="shipping_zip" name="shipping_zip" required>
                                @error('shipping_zip')
                                    <small style="color: #ef4444;">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="shipping_country">Country *</label>
                                <input type="text" id="shipping_country" name="shipping_country" required>
                                @error('shipping_country')
                                    <small style="color: #ef4444;">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Payment Method -->
                    <div class="checkout-step">
                        <div class="step-header">
                            <div class="step-number">4</div>
                            <h2 class="step-title">Select Payment Method</h2>
                        </div>

                        @foreach($paymentMethods as $key => $method)
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="{{ $key }}" required>
                                <div style="display: flex; align-items: center; gap: 1rem; margin-left: 2rem; margin-top: -1.5rem;">
                                    <span style="font-size: 1.5rem;">{{ $method['icon'] }}</span>
                                    <div style="font-weight: 600; color: #1e293b;">{{ $method['name'] }}</div>
                                </div>
                            </label>
                        @endforeach

                        <div style="background: #fef3c7; border: 2px solid #f59e0b; color: #92400e; padding: 1rem; border-radius: 8px; margin-top: 1.5rem;">
                            <strong>Note:</strong> Payment processing is not yet implemented. This is a demo checkout flow.
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 2rem;">
                        <button type="submit" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 3rem; font-size: 1.1rem; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);">
                            Place Order
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column - Order Summary -->
            <div style="grid-column: span 4;">
                <div class="order-summary-box">
                    <h3 style="margin-top: 0; margin-bottom: 1.5rem; font-size: 1.3rem; color: #1e293b;">Order Summary</h3>

                    <div class="summary-row">
                        <span>Subtotal ({{ $totalPages }} pages)</span>
                        <span style="font-weight: 600;">${{ number_format($cartTotal, 2) }}</span>
                    </div>

                    <div class="summary-row">
                        <span>Shipping</span>
                        <span style="font-weight: 600;" x-text="shippingPrice"></span>
                    </div>

                    <div class="summary-row total">
                        <span>Total</span>
                        <span style="color: #059669;" x-text="'$' + grandTotal.toFixed(2)"></span>
                    </div>

                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #e2e8f0;">
                        <a href="{{ route('order') }}" style="display: block; text-align: center; color: #3b82f6; text-decoration: none; font-weight: 600;">
                            ← Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function checkoutData() {
            return {
                shippingMethod: 'standard',
                cartTotal: {{ $cartTotal }},
                shippingMethods: @json($shippingMethods),

                get shippingPrice() {
                    if (!this.shippingMethod) return '$0.00';
                    return '$' + this.shippingMethods[this.shippingMethod].price.toFixed(2);
                },

                get grandTotal() {
                    if (!this.shippingMethod) return this.cartTotal;
                    return this.cartTotal + this.shippingMethods[this.shippingMethod].price;
                }
            }
        }
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>
</x-layout>

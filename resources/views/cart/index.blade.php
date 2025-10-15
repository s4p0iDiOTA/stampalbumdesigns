<x-layout>
    <style>
        .cart-container {
            max-width: 1000px;
            margin: 2rem auto;
        }

        .cart-item {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .cart-summary {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            position: sticky;
            top: 20px;
        }
    </style>

    <main class="container cart-container">
        <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 2rem;">Shopping Cart</h1>

        @if (session('success'))
            <div style="background: #d1fae5; border: 2px solid #10b981; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="background: #fee2e2; border: 2px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                {{ session('error') }}
            </div>
        @endif

        @if (empty($cart))
            <div style="text-align: center; padding: 4rem 0;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🛒</div>
                <h2 style="color: #64748b; margin-bottom: 1rem;">Your cart is empty</h2>
                <p style="color: #94a3b8; margin-bottom: 2rem;">Start building your stamp album order!</p>
                <a href="{{ route('order') }}" style="display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="grid">
                <!-- Cart Items -->
                <div style="grid-column: span 8;">
                    @php
                        $cartTotal = 0;
                        $totalPages = 0;
                    @endphp

                    @foreach ($cart as $itemId => $item)
                        @if(isset($item['total']) && isset($item['quantity']))
                            @php
                                $cartTotal += floatval($item['total']) * intval($item['quantity']);
                                if(isset($item['order_groups'])) {
                                    foreach($item['order_groups'] as $group) {
                                        $totalPages += intval($group['totalPages']) * intval($item['quantity']);
                                    }
                                }
                            @endphp

                            <div class="cart-item">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div>
                                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.2rem; color: #1e293b;">
                                            Order #{{ substr($itemId, -8) }}
                                        </h3>
                                        <div style="color: #64748b; font-size: 0.9rem;">
                                            Added: {{ isset($item['created_at']) ? \Carbon\Carbon::parse($item['created_at'])->format('M d, Y g:i A') : 'Recently' }}
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 1.5rem; font-weight: 700; color: #059669;">
                                            ${{ number_format($item['total'], 2) }}
                                        </div>
                                        <div style="color: #64748b; font-size: 0.9rem;">
                                            Quantity: {{ $item['quantity'] }}
                                        </div>
                                    </div>
                                </div>

                                @if(isset($item['order_groups']))
                                    <div style="border-top: 1px solid #e2e8f0; padding-top: 1rem; margin-top: 1rem;">
                                        <h4 style="margin: 0 0 0.75rem 0; font-size: 0.9rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Items</h4>
                                        @foreach($item['order_groups'] as $group)
                                            <div style="padding: 0.5rem 0; display: flex; justify-content: space-between; align-items: center;">
                                                <div>
                                                    <strong style="color: #1e293b;">{{ $group['country'] }}</strong>
                                                    <span style="color: #64748b; font-size: 0.9rem; margin-left: 0.5rem;">
                                                        ({{ $group['actualYearRange'] }})
                                                    </span>
                                                </div>
                                                <div style="color: #64748b; font-size: 0.9rem;">
                                                    {{ $group['totalPages'] }} pages × ${{ number_format($group['paperType'], 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                                    <form action="{{ route('checkout.remove', $itemId) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-weight: 600;" onclick="return confirm('Remove this item from cart?')">
                                            🗑️ Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Cart Summary -->
                <div style="grid-column: span 4;">
                    <div class="cart-summary">
                        <h3 style="margin-top: 0; margin-bottom: 1.5rem; font-size: 1.3rem; color: #1e293b;">Order Summary</h3>

                        <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="color: #64748b;">Items</span>
                                <span style="font-weight: 600;">{{ count($cart) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="color: #64748b;">Total Pages</span>
                                <span style="font-weight: 600;">{{ $totalPages }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Subtotal</span>
                                <span style="font-weight: 600;">${{ number_format($cartTotal, 2) }}</span>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: 700; margin-bottom: 1.5rem;">
                            <span>Total</span>
                            <span style="color: #059669;">${{ number_format($cartTotal, 2) }}</span>
                        </div>

                        <a href="{{ route('checkout.index') }}" style="display: block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem; border-radius: 8px; text-decoration: none; font-weight: 700; text-align: center; margin-bottom: 1rem;">
                            Proceed to Checkout
                        </a>

                        <a href="{{ route('order') }}" style="display: block; background: white; color: #3b82f6; padding: 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center; border: 2px solid #3b82f6;">
                            Continue Shopping
                        </a>

                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #e2e8f0; text-align: center;">
                            <a href="{{ route('checkout.clear') }}" style="color: #ef4444; text-decoration: none; font-weight: 600; font-size: 0.9rem;" onclick="return confirm('Clear all items from cart?')">
                                Clear Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</x-layout>

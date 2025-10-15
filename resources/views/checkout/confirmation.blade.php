<x-layout>
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 3rem auto;
            text-align: center;
        }

        .success-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }

        .order-details {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }
    </style>

    <main class="container confirmation-container">
        <div class="success-icon">✅</div>
        <h1 style="font-size: 2.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Order Confirmed!</h1>
        <p style="font-size: 1.1rem; color: #64748b; margin-bottom: 2rem;">
            Thank you for your order. We've received it and will begin processing shortly.
        </p>

        <div style="background: #eff6ff; border: 2px solid #3b82f6; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
            <div style="font-size: 0.9rem; color: #1e40af; margin-bottom: 0.5rem;">Order Number</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #1e40af;">{{ $order['order_number'] }}</div>
        </div>

        <div class="order-details">
            <h2 style="margin-top: 0; margin-bottom: 1.5rem; font-size: 1.5rem; color: #1e293b;">Order Details</h2>

            <div class="detail-row">
                <span style="font-weight: 600;">Order Date:</span>
                <span>{{ \Carbon\Carbon::parse($order['order_date'])->format('F d, Y g:i A') }}</span>
            </div>

            <div class="detail-row">
                <span style="font-weight: 600;">Shipping Method:</span>
                <span>{{ ucwords(str_replace('_', ' ', $order['shipping']['shipping_method'])) }}</span>
            </div>

            <div class="detail-row">
                <span style="font-weight: 600;">Payment Method:</span>
                <span>{{ ucwords(str_replace('_', ' ', $order['shipping']['payment_method'])) }}</span>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e2e8f0;">
                <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1.2rem; color: #1e293b;">Shipping Address</h3>
                <div style="color: #64748b; line-height: 1.8;">
                    {{ $order['shipping']['shipping_name'] }}<br>
                    {{ $order['shipping']['shipping_address'] }}<br>
                    {{ $order['shipping']['shipping_city'] }}, {{ $order['shipping']['shipping_state'] }} {{ $order['shipping']['shipping_zip'] }}<br>
                    {{ $order['shipping']['shipping_country'] }}
                </div>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e2e8f0;">
                <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1.2rem; color: #1e293b;">Order Items</h3>
                @foreach($order['cart'] as $itemId => $item)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-weight: 600;">Order #{{ substr($itemId, -8) }}</span>
                            <span style="font-weight: 700; color: #059669;">${{ number_format($item['total'], 2) }}</span>
                        </div>
                        @if(isset($item['order_groups']))
                            @foreach($item['order_groups'] as $group)
                                <div style="font-size: 0.9rem; color: #64748b; padding: 0.25rem 0;">
                                    {{ $group['country'] }} ({{ $group['actualYearRange'] }}) - {{ $group['totalPages'] }} pages
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div style="margin-top: 3rem; display: flex; gap: 1rem; justify-content: center;">
            <a href="{{ route('order') }}" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                Create Another Order
            </a>
            <a href="/" style="background: #e2e8f0; color: #1e293b; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                Return to Home
            </a>
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px;">
            <p style="margin: 0; color: #92400e;">
                <strong>Note:</strong> A confirmation email will be sent to your registered email address.
            </p>
        </div>
    </main>
</x-layout>

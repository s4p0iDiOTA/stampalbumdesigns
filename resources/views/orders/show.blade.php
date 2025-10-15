<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Order #{{ $order->reference }}
            </h2>
            <a href="{{ route('orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                ← Back to Orders
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Order Summary -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Order Summary</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Order Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $order->created_at->format('F d, Y \a\t g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($order->status === 'awaiting-payment') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'payment-received') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucwords(str_replace('-', ' ', $order->status)) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subtotal</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${{ number_format($order->sub_total->value / 100, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Shipping</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${{ number_format($order->shipping_total->value / 100, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tax</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${{ number_format($order->tax_total->value / 100, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-gray-100">${{ number_format($order->total->value / 100, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Items</h3>
                    <div class="space-y-4">
                        @foreach($order->lines as $line)
                            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $line->description }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Quantity: {{ $line->quantity }}
                                    </p>
                                </div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    ${{ number_format($line->total->value / 100, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            @if($order->shippingAddress)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Shipping Address</h3>
                    <address class="not-italic text-sm text-gray-700 dark:text-gray-300">
                        {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}<br>
                        {{ $order->shippingAddress->line_one }}<br>
                        @if($order->shippingAddress->line_two)
                            {{ $order->shippingAddress->line_two }}<br>
                        @endif
                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postcode }}<br>
                        {{ $order->shippingAddress->country?->name }}
                    </address>
                </div>
            </div>
            @endif

            <!-- Billing Address -->
            @if($order->billingAddress)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Billing Address</h3>
                    <address class="not-italic text-sm text-gray-700 dark:text-gray-300">
                        {{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}<br>
                        {{ $order->billingAddress->line_one }}<br>
                        @if($order->billingAddress->line_two)
                            {{ $order->billingAddress->line_two }}<br>
                        @endif
                        {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postcode }}<br>
                        {{ $order->billingAddress->country?->name }}
                    </address>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

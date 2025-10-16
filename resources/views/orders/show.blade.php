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
                    <div class="space-y-6">
                        @foreach($order->lines as $line)
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $line->description }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Quantity: {{ $line->quantity }}
                                        </p>
                                        @if(isset($line->meta['total_pages']))
                                            <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                                                Total Pages: {{ $line->meta['total_pages'] }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        ${{ number_format($line->total->value / 100, 2) }}
                                    </div>
                                </div>

                                @if(isset($line->meta['order_groups']) && is_array($line->meta['order_groups']))
                                    <div class="mt-3 space-y-3">
                                        <h5 class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Order Details:</h5>
                                        @foreach($line->meta['order_groups'] as $group)
                                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                    <div>
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">Country:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">{{ $group['country'] ?? 'N/A' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">Year Range:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">{{ $group['actualYearRange'] ?? $group['yearRange'] ?? 'N/A' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">Pages:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">{{ $group['totalPages'] ?? 'N/A' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">Files:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">{{ $group['totalFiles'] ?? 'N/A' }}</span>
                                                    </div>
                                                    @if(isset($group['paperType']))
                                                        <div class="col-span-2">
                                                            <span class="font-medium text-gray-700 dark:text-gray-300">Paper Type:</span>
                                                            <span class="text-gray-900 dark:text-gray-100">
                                                                @if($group['paperType'] == '0.20')
                                                                    Heavyweight 3-hole ($0.20/page)
                                                                @elseif($group['paperType'] == '0.30')
                                                                    Scott International / Minkus 2-hole ($0.30/page)
                                                                @elseif($group['paperType'] == '0.35')
                                                                    Scott Specialized ($0.35/page)
                                                                @else
                                                                    ${{ $group['paperType'] }}/page
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if(isset($group['periods']) && is_array($group['periods']) && count($group['periods']) > 0)
                                                    <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Files Selected:</span>
                                                        <ul class="mt-1 space-y-1">
                                                            @foreach($group['periods'] as $period)
                                                                <li class="text-xs text-gray-600 dark:text-gray-400">
                                                                    • {{ $period['description'] ?? 'N/A' }} ({{ $period['pages'] ?? $period['pagesInRange'] ?? 0 }} pages)
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
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

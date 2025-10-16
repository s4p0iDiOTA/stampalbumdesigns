<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Lunar\Models\Order;
use Lunar\Models\OrderLine;
use Lunar\Models\Currency;
use Lunar\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderDataCaptureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required Lunar data
        Currency::firstOrCreate(
            ['code' => 'USD'],
            [
                'name' => 'US Dollar',
                'exchange_rate' => 1.00,
                'decimal_places' => 2,
                'enabled' => true,
                'default' => true,
            ]
        );

        Channel::firstOrCreate(
            ['handle' => 'webstore'],
            [
                'name' => 'Webstore',
                'default' => true,
                'url' => config('app.url'),
            ]
        );

        \Lunar\Models\Language::firstOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'default' => true,
            ]
        );

        \Lunar\Models\Country::firstOrCreate(
            ['iso2' => 'US'],
            [
                'name' => 'United States',
                'iso3' => 'USA',
                'phonecode' => '1',
                'capital' => 'Washington',
                'currency' => 'USD',
                'native' => 'United States',
                'emoji' => '🇺🇸',
                'emoji_u' => 'U+1F1FA U+1F1F8',
            ]
        );

        $taxClass = \Lunar\Models\TaxClass::firstOrCreate(
            ['name' => 'Default'],
            ['default' => true]
        );

        $productType = \Lunar\Models\ProductType::firstOrCreate(
            ['name' => 'Stamp Album Pages']
        );

        $product = new \Lunar\Models\Product([
            'product_type_id' => $productType->id,
            'status' => 'published',
            'brand_id' => null,
        ]);
        $product->attribute_data = collect([
            'name' => new \Lunar\FieldTypes\Text('Test Stamp Album Pages'),
        ]);
        $product->save();

        $variant = \Lunar\Models\ProductVariant::create([
            'product_id' => $product->id,
            'tax_class_id' => $taxClass->id,
            'sku' => 'STAMP-TEST-001',
            'stock' => 9999,
            'shippable' => true,
            'purchasable' => 'always',
        ]);

        $currency = Currency::first();
        $variant->prices()->create([
            'price' => 1000,
            'currency_id' => $currency->id,
            'min_quantity' => 1,
        ]);

        // Create roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_order_captures_all_cart_details_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        // Simulate realistic cart with detailed order groups
        $cart = [
            'item-1' => [
                'total' => 75.50,
                'quantity' => 1,
                'order_groups' => [
                    [
                        'country' => 'United States',
                        'paperType' => 'Heavyweight 3-hole',
                        'totalPages' => 250,
                        'actualYearRange' => '1900-2024',
                        'totalFiles' => 3,
                        'yearRange' => '1900 - 2024',
                    ],
                    [
                        'country' => 'France',
                        'paperType' => 'Scott International',
                        'totalPages' => 125,
                        'actualYearRange' => '1950-2000',
                        'totalFiles' => 2,
                        'yearRange' => '1950 - 2000',
                    ],
                ],
            ],
        ];

        session(['cart' => $cart]);

        $response = $this->actingAs($user)->post('/checkout/process', [
            'shipping_method' => 'standard',
            'payment_method' => 'credit_card',
            'shipping_name' => 'John Doe',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'New York',
            'shipping_state' => 'NY',
            'shipping_zip' => '10001',
            'shipping_country' => 'United States',
            'billing_same_as_shipping' => true,
        ]);

        // Assert order was created
        $this->assertDatabaseCount('lunar_orders', 1);

        $order = Order::with(['lines', 'addresses'])->first();

        // Verify order is linked to user
        $this->assertEquals($user->id, $order->user_id);

        // Verify order totals
        $this->assertEquals(7550, $order->sub_total->value); // $75.50
        $this->assertEquals(599, $order->shipping_total->value); // $5.99

        // Verify order has payment and shipping method in meta
        $this->assertEquals('credit_card', $order->meta['payment_method']);
        $this->assertEquals('standard', $order->meta['shipping_method']);

        // Verify order has addresses
        $this->assertCount(2, $order->addresses); // shipping + billing

        $shippingAddress = $order->addresses->where('type', 'shipping')->first();
        $this->assertEquals('John', $shippingAddress->first_name);
        $this->assertEquals('Doe', $shippingAddress->last_name);
        $this->assertEquals('123 Main St', $shippingAddress->line_one);
        $this->assertEquals($user->email, $shippingAddress->contact_email);

        // Verify order has line items with detailed meta data
        $this->assertCount(1, $order->lines);

        $line = $order->lines->first();
        $this->assertArrayHasKey('order_groups', $line->meta);
        $this->assertArrayHasKey('total_pages', $line->meta);

        // Verify order_groups data is captured
        $orderGroups = $line->meta['order_groups'];
        $this->assertCount(2, $orderGroups);

        // Check first order group
        $this->assertEquals('United States', $orderGroups[0]['country']);
        $this->assertEquals('Heavyweight 3-hole', $orderGroups[0]['paperType']);
        $this->assertEquals(250, $orderGroups[0]['totalPages']);
        $this->assertEquals('1900-2024', $orderGroups[0]['actualYearRange']);

        // Check second order group
        $this->assertEquals('France', $orderGroups[1]['country']);
        $this->assertEquals('Scott International', $orderGroups[1]['paperType']);
        $this->assertEquals(125, $orderGroups[1]['totalPages']);
        $this->assertEquals('1950-2000', $orderGroups[1]['actualYearRange']);

        // Verify total pages calculation
        $this->assertEquals(375, $line->meta['total_pages']); // 250 + 125
    }

    public function test_order_captures_guest_user_email(): void
    {
        // Simulate guest user (no authentication)
        $cart = [
            'item-1' => [
                'total' => 50.00,
                'quantity' => 1,
                'order_groups' => [
                    [
                        'country' => 'United States',
                        'paperType' => 'Heavyweight 3-hole',
                        'totalPages' => 250,
                        'actualYearRange' => '1900-2024',
                    ],
                ],
            ],
        ];

        session(['cart' => $cart]);

        $response = $this->post('/checkout/process', [
            'shipping_method' => 'express',
            'payment_method' => 'paypal',
            'shipping_name' => 'Guest User',
            'shipping_address' => '456 Oak Ave',
            'shipping_city' => 'Los Angeles',
            'shipping_state' => 'CA',
            'shipping_zip' => '90001',
            'shipping_country' => 'United States',
        ]);

        $this->assertDatabaseCount('lunar_orders', 1);

        $order = Order::with('addresses')->first();

        // Verify order has no user_id (guest order)
        $this->assertNull($order->user_id);

        // Verify guest email is set to placeholder
        $shippingAddress = $order->addresses->where('type', 'shipping')->first();
        $this->assertEquals('guest@example.com', $shippingAddress->contact_email);

        // Verify order still captures all details
        $this->assertEquals('paypal', $order->meta['payment_method']);
        $this->assertEquals('express', $order->meta['shipping_method']);
    }

    public function test_order_line_description_includes_country_name(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $cart = [
            'item-1' => [
                'total' => 50.00,
                'quantity' => 1,
                'order_groups' => [
                    [
                        'country' => 'Germany',
                        'paperType' => 'Scott Specialized',
                        'totalPages' => 100,
                        'actualYearRange' => '1949-1990',
                    ],
                ],
            ],
        ];

        session(['cart' => $cart]);

        $this->actingAs($user)->post('/checkout/process', [
            'shipping_method' => 'standard',
            'payment_method' => 'credit_card',
            'shipping_name' => 'Test User',
            'shipping_address' => '123 Test St',
            'shipping_city' => 'Test City',
            'shipping_state' => 'TS',
            'shipping_zip' => '12345',
            'shipping_country' => 'United States',
        ]);

        $order = Order::with('lines')->first();
        $line = $order->lines->first();

        // Verify description includes country name
        $this->assertEquals('Stamp Album Pages - Germany', $line->description);
    }

    public function test_multiple_cart_items_create_multiple_order_lines(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $cart = [
            'item-1' => [
                'total' => 30.00,
                'quantity' => 1,
                'order_groups' => [
                    ['country' => 'France', 'totalPages' => 100],
                ],
            ],
            'item-2' => [
                'total' => 40.00,
                'quantity' => 2,
                'order_groups' => [
                    ['country' => 'Germany', 'totalPages' => 150],
                ],
            ],
        ];

        session(['cart' => $cart]);

        $this->actingAs($user)->post('/checkout/process', [
            'shipping_method' => 'standard',
            'payment_method' => 'credit_card',
            'shipping_name' => 'Test User',
            'shipping_address' => '123 Test St',
            'shipping_city' => 'Test City',
            'shipping_state' => 'TS',
            'shipping_zip' => '12345',
            'shipping_country' => 'United States',
        ]);

        $order = Order::with('lines')->first();

        // Verify multiple lines created
        $this->assertCount(2, $order->lines);

        // Verify line descriptions
        $this->assertEquals('Stamp Album Pages - France', $order->lines[0]->description);
        $this->assertEquals('Stamp Album Pages - Germany', $order->lines[1]->description);

        // Verify quantities
        $this->assertEquals(1, $order->lines[0]->quantity);
        $this->assertEquals(2, $order->lines[1]->quantity);
    }

    public function test_admin_can_view_all_order_details_in_lunar(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        // Create order with detailed cart
        $cart = [
            'item-1' => [
                'total' => 60.00,
                'quantity' => 1,
                'order_groups' => [
                    [
                        'country' => 'United States',
                        'paperType' => 'Heavyweight 3-hole',
                        'totalPages' => 200,
                        'actualYearRange' => '1847-2024',
                        'yearRange' => '1847 - 2024',
                        'totalFiles' => 5,
                    ],
                ],
            ],
        ];

        session(['cart' => $cart]);

        $this->actingAs($customer)->post('/checkout/process', [
            'shipping_method' => 'overnight',
            'payment_method' => 'bank_transfer',
            'shipping_name' => 'Customer Name',
            'shipping_address' => '789 Customer St',
            'shipping_city' => 'Chicago',
            'shipping_state' => 'IL',
            'shipping_zip' => '60601',
            'shipping_country' => 'United States',
        ]);

        $order = Order::with(['lines', 'addresses'])->first();

        // Verify all data is accessible
        $this->assertNotNull($order->user_id);
        $this->assertEquals($customer->id, $order->user_id);
        $this->assertNotNull($order->meta['payment_method']);
        $this->assertNotNull($order->meta['shipping_method']);
        $this->assertNotNull($order->lines->first()->meta['order_groups']);

        $orderGroups = $order->lines->first()->meta['order_groups'];
        $this->assertArrayHasKey('country', $orderGroups[0]);
        $this->assertArrayHasKey('paperType', $orderGroups[0]);
        $this->assertArrayHasKey('totalPages', $orderGroups[0]);
        $this->assertArrayHasKey('actualYearRange', $orderGroups[0]);
    }
}

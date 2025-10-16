<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Lunar\Models\Order;
use Lunar\Models\Currency;
use Lunar\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required Lunar data (use firstOrCreate to avoid duplicates)
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

        // Create Language
        \Lunar\Models\Language::firstOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'default' => true,
            ]
        );

        // Create Country for address lookups
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

        // Create TaxClass
        $taxClass = \Lunar\Models\TaxClass::firstOrCreate(
            ['name' => 'Default'],
            ['default' => true]
        );

        // Create ProductType
        $productType = \Lunar\Models\ProductType::firstOrCreate(
            ['name' => 'Stamp Album Pages']
        );

        // Create Product
        $product = new \Lunar\Models\Product([
            'product_type_id' => $productType->id,
            'status' => 'published',
            'brand_id' => null,
        ]);
        $product->attribute_data = collect([
            'name' => new \Lunar\FieldTypes\Text('Test Stamp Album Pages'),
        ]);
        $product->save();

        // Create ProductVariant
        $variant = \Lunar\Models\ProductVariant::create([
            'product_id' => $product->id,
            'tax_class_id' => $taxClass->id,
            'sku' => 'STAMP-TEST-001',
            'stock' => 9999,
            'shippable' => true,
            'purchasable' => 'always',
        ]);

        // Create Price for variant
        $currency = Currency::first();
        $variant->prices()->create([
            'price' => 1000, // $10.00 in cents
            'currency_id' => $currency->id,
            'min_quantity' => 1,
        ]);

        // Create roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_checkout_creates_lunar_order(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        // Simulate cart session
        session([
            'cart' => [
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
            ],
        ]);

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

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('awaiting-payment', $order->status);
        $this->assertEquals(5000, $order->sub_total->value); // $50.00 in cents
        $this->assertEquals(599, $order->shipping_total->value); // $5.99 in cents

        // Assert order has addresses
        $this->assertCount(2, $order->addresses); // shipping + billing

        // Assert order has line items
        $this->assertCount(1, $order->lines);
        $this->assertEquals('Stamp Album Pages - United States', $order->lines->first()->description);

        // Assert redirects to confirmation
        $response->assertRedirect(route('checkout.confirmation'));
        $response->assertSessionHas('success');
    }

    public function test_checkout_calculates_totals_correctly(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        // Cart with multiple items
        session([
            'cart' => [
                'item-1' => [
                    'total' => 25.00,
                    'quantity' => 2,
                    'order_groups' => [
                        ['country' => 'France', 'totalPages' => 125],
                    ],
                ],
                'item-2' => [
                    'total' => 35.00,
                    'quantity' => 1,
                    'order_groups' => [
                        ['country' => 'Germany', 'totalPages' => 175],
                    ],
                ],
            ],
        ]);

        $this->actingAs($user)->post('/checkout/process', [
            'shipping_method' => 'express',
            'payment_method' => 'paypal',
            'shipping_name' => 'Jane Smith',
            'shipping_address' => '456 Oak Ave',
            'shipping_city' => 'Los Angeles',
            'shipping_state' => 'CA',
            'shipping_zip' => '90001',
            'shipping_country' => 'United States',
        ]);

        $order = Order::first();

        // Subtotal: (25 * 2) + 35 = 85
        // Shipping: express = 15.99
        // Total: 85 + 15.99 = 100.99
        $this->assertEquals(8500, $order->sub_total->value);
        $this->assertEquals(1599, $order->shipping_total->value);
        $this->assertEquals(10099, $order->total->value);
    }

    public function test_checkout_requires_cart(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        // No cart in session
        $response = $this->actingAs($user)->post('/checkout/process', [
            'shipping_method' => 'standard',
            'payment_method' => 'credit_card',
            'shipping_name' => 'John Doe',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'New York',
            'shipping_state' => 'NY',
            'shipping_zip' => '10001',
            'shipping_country' => 'United States',
        ]);

        $response->assertRedirect(route('order'));
        $response->assertSessionHas('error', 'Your cart is empty.');
        $this->assertDatabaseCount('lunar_orders', 0);
    }

    public function test_checkout_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        session(['cart' => ['item-1' => ['total' => 50, 'quantity' => 1, 'order_groups' => []]]]);

        $response = $this->actingAs($user)->post('/checkout/process', [
            // Missing required fields
            'shipping_method' => 'standard',
        ]);

        $response->assertSessionHasErrors([
            'payment_method',
            'shipping_name',
            'shipping_address',
            'shipping_city',
            'shipping_state',
            'shipping_zip',
            'shipping_country',
        ]);

        $this->assertDatabaseCount('lunar_orders', 0);
    }

    public function test_checkout_clears_cart_after_order(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        session(['cart' => ['item-1' => ['total' => 50, 'quantity' => 1, 'order_groups' => []]]]);

        $this->actingAs($user)->post('/checkout/process', [
            'shipping_method' => 'standard',
            'payment_method' => 'credit_card',
            'shipping_name' => 'John Doe',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'New York',
            'shipping_state' => 'NY',
            'shipping_zip' => '10001',
            'shipping_country' => 'United States',
        ]);

        $this->assertNull(session('cart'));
        $this->assertNotNull(session('last_order'));
    }
}

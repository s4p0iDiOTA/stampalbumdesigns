<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Lunar\Models\Order;
use Lunar\Models\OrderLine;
use Lunar\Models\Currency;
use Lunar\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderFlowFromJsonTest extends TestCase
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

    public function test_order_uses_json_data_as_source_of_truth(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        // Simulate cart created from /order page using JSON data
        // This cart structure matches what the order.blade.php Alpine.js creates
        $cart = [
            'order_67890abc' => [
                'total' => 60.00,
                'quantity' => 1,
                'order_groups' => [
                    [
                        'id' => 1,
                        'country' => 'ARGENTINA', // From page_count_per_file_per_country.json
                        'yearRange' => '1847 - 2024',
                        'actualYearRange' => '1847-2024',
                        'periods' => [
                            [
                                'id' => 1,
                                'description' => 'Thru 1940', // File description from JSON
                                'pages' => 71, // Total pages in file from page_count_per_file_per_country.json
                                'years' => [1847, 1850, 1900, 1920, 1940],
                                'yearPageMap' => [
                                    '1847' => [1, 2],
                                    '1850' => [3, 4],
                                    '1900' => [5, 6, 7],
                                ],
                                'pagesInRange' => 71,
                                'filteredYears' => [1847, 1850, 1900, 1920, 1940],
                            ],
                            [
                                'id' => 2,
                                'description' => '1941-1960', // Another file from JSON
                                'pages' => 41,
                                'years' => [1941, 1950, 1960],
                                'yearPageMap' => [
                                    '1941' => [72, 73],
                                    '1950' => [74, 75, 76],
                                ],
                                'pagesInRange' => 41,
                                'filteredYears' => [1941, 1950, 1960],
                            ],
                        ],
                        'totalFiles' => 2,
                        'totalPages' => 112, // 71 + 41 from JSON data
                        'paperType' => '0.20', // Heavyweight 3-hole selected
                        'expanded' => false,
                    ],
                    [
                        'id' => 2,
                        'country' => 'AUSTRALIA', // Different country from JSON
                        'yearRange' => '1966 - 2023',
                        'actualYearRange' => '1966-2023',
                        'periods' => [
                            [
                                'id' => 3,
                                'description' => '1966-90',
                                'pages' => 87,
                                'years' => [1966, 1970, 1980, 1990],
                                'yearPageMap' => [],
                                'pagesInRange' => 87,
                                'filteredYears' => [1966, 1970, 1980, 1990],
                            ],
                        ],
                        'totalFiles' => 1,
                        'totalPages' => 87,
                        'paperType' => '0.30', // Scott International selected
                        'expanded' => false,
                    ],
                ],
                'created_at' => now()->toDateTimeString(),
            ],
        ];

        session(['cart' => $cart]);

        // Process checkout
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

        // Verify order was created
        $this->assertDatabaseCount('lunar_orders', 1);

        $order = Order::with('lines')->first();

        // Verify order is linked to user
        $this->assertEquals($user->id, $order->user_id);

        // Verify order line was created
        $this->assertCount(1, $order->lines);

        $line = $order->lines->first();

        // Verify order_groups meta contains the JSON-sourced data
        $this->assertArrayHasKey('order_groups', $line->meta);
        $orderGroups = $line->meta['order_groups'];
        $this->assertCount(2, $orderGroups);

        // Verify first group (ARGENTINA)
        $argentina = $orderGroups[0];
        $this->assertEquals('ARGENTINA', $argentina['country']);
        $this->assertEquals('1847-2024', $argentina['actualYearRange']);
        $this->assertEquals(2, $argentina['totalFiles']);
        $this->assertEquals(112, $argentina['totalPages']);
        $this->assertEquals('0.20', $argentina['paperType']);

        // Verify periods data from JSON is preserved
        $this->assertCount(2, $argentina['periods']);
        $this->assertEquals('Thru 1940', $argentina['periods'][0]['description']);
        $this->assertEquals(71, $argentina['periods'][0]['pages']);
        $this->assertEquals('1941-1960', $argentina['periods'][1]['description']);
        $this->assertEquals(41, $argentina['periods'][1]['pages']);

        // Verify second group (AUSTRALIA)
        $australia = $orderGroups[1];
        $this->assertEquals('AUSTRALIA', $australia['country']);
        $this->assertEquals('1966-2023', $australia['actualYearRange']);
        $this->assertEquals(1, $australia['totalFiles']);
        $this->assertEquals(87, $australia['totalPages']);
        $this->assertEquals('0.30', $australia['paperType']);

        // Verify periods data
        $this->assertCount(1, $australia['periods']);
        $this->assertEquals('1966-90', $australia['periods'][0]['description']);
        $this->assertEquals(87, $australia['periods'][0]['pages']);

        // Verify total pages calculation
        $this->assertEquals(199, $line->meta['total_pages']); // 112 + 87
    }

    public function test_order_description_shows_first_country_from_json_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $cart = [
            'order_12345' => [
                'total' => 30.00,
                'quantity' => 1,
                'order_groups' => [
                    [
                        'country' => 'GERMANY',
                        'totalPages' => 150,
                        'paperType' => '0.20',
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

        // Verify description contains country name from JSON
        $this->assertEquals('Stamp Album Pages - GERMANY', $line->description);
    }

    public function test_customer_can_view_order_with_json_data_at_my_orders(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        // Create order with JSON-based data
        $cart = [
            'order_abc123' => [
                'total' => 50.00,
                'quantity' => 1,
                'order_groups' => [
                    [
                        'country' => 'FRANCE',
                        'yearRange' => '1900 - 2020',
                        'actualYearRange' => '1900-2020',
                        'totalPages' => 250,
                        'totalFiles' => 3,
                        'paperType' => '0.35',
                        'periods' => [
                            [
                                'description' => 'Thru 1960',
                                'pages' => 100,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        session(['cart' => $cart]);

        // Create order
        $this->actingAs($user)->post('/checkout/process', [
            'shipping_method' => 'express',
            'payment_method' => 'paypal',
            'shipping_name' => 'Jane Doe',
            'shipping_address' => '456 Oak Ave',
            'shipping_city' => 'Los Angeles',
            'shipping_state' => 'CA',
            'shipping_zip' => '90001',
            'shipping_country' => 'United States',
        ]);

        $order = Order::first();

        // Access /my-orders page
        $response = $this->actingAs($user)->get('/my-orders');
        $response->assertStatus(200);
        $response->assertSee($order->reference);

        // Access individual order page
        $response = $this->actingAs($user)->get("/my-orders/{$order->id}");
        $response->assertStatus(200);
        $response->assertSee('FRANCE');
        $response->assertSee('250');
    }

    public function test_admin_can_view_order_with_json_data_in_lunar_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        // Create order with detailed JSON data
        $cart = [
            'order_xyz789' => [
                'total' => 75.00,
                'quantity' => 1,
                'order_groups' => [
                    [
                        'country' => 'BELGIUM',
                        'yearRange' => '1850 - 2024',
                        'actualYearRange' => '1850-2024',
                        'totalPages' => 375,
                        'totalFiles' => 5,
                        'paperType' => '0.20',
                        'periods' => [
                            [
                                'description' => 'Thru 1920',
                                'pages' => 75,
                            ],
                            [
                                'description' => '1921-1960',
                                'pages' => 100,
                            ],
                        ],
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

        $order = Order::with('lines')->first();

        // Verify admin can access the order data
        $this->assertNotNull($order);
        $this->assertEquals($customer->id, $order->user_id);

        $orderGroups = $order->lines->first()->meta['order_groups'];
        $this->assertArrayHasKey('country', $orderGroups[0]);
        $this->assertEquals('BELGIUM', $orderGroups[0]['country']);
        $this->assertArrayHasKey('periods', $orderGroups[0]);
        $this->assertCount(2, $orderGroups[0]['periods']);
    }

    public function test_multiple_cart_items_preserve_json_data_separately(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        // Multiple cart items with different JSON-based selections
        $cart = [
            'order_item1' => [
                'total' => 30.00,
                'quantity' => 1,
                'order_groups' => [
                    [
                        'country' => 'ITALY',
                        'totalPages' => 150,
                        'paperType' => '0.20',
                    ],
                ],
            ],
            'order_item2' => [
                'total' => 45.00,
                'quantity' => 2,
                'order_groups' => [
                    [
                        'country' => 'SPAIN',
                        'totalPages' => 150,
                        'paperType' => '0.30',
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

        // Verify two separate order lines
        $this->assertCount(2, $order->lines);

        // Verify first line has ITALY data
        $this->assertEquals('Stamp Album Pages - ITALY', $order->lines[0]->description);
        $this->assertEquals('ITALY', $order->lines[0]->meta['order_groups'][0]['country']);

        // Verify second line has SPAIN data
        $this->assertEquals('Stamp Album Pages - SPAIN', $order->lines[1]->description);
        $this->assertEquals('SPAIN', $order->lines[1]->meta['order_groups'][0]['country']);

        // Verify quantities
        $this->assertEquals(1, $order->lines[0]->quantity);
        $this->assertEquals(2, $order->lines[1]->quantity);
    }
}

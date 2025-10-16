<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\OrderAddress;
use Lunar\Models\OrderLine;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Base\ValueObjects\Cart\TaxBreakdown;
use Lunar\FieldTypes\Text;
use Tests\TestCase;

class LunarIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required Lunar entities (use firstOrCreate to avoid duplicates)
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

        Language::firstOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'default' => true,
            ]
        );

        CustomerGroup::firstOrCreate(
            ['handle' => 'retail'],
            [
                'name' => 'Retail',
                'default' => true,
            ]
        );

        Country::firstOrCreate(
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

        $taxClass = TaxClass::firstOrCreate(
            ['name' => 'Default'],
            ['default' => true]
        );

        $productType = ProductType::firstOrCreate(
            ['name' => 'Stamp Album Pages']
        );

        $product = new Product([
            'product_type_id' => $productType->id,
            'status' => 'published',
            'brand_id' => null,
        ]);
        $product->attribute_data = collect([
            'name' => new Text('Test Stamp Album Pages'),
        ]);
        $product->save();

        $variant = ProductVariant::create([
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

    public function test_order_can_be_created_with_all_required_fields(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $currency = Currency::first();
        $channel = Channel::first();
        $country = Country::first();
        $variant = ProductVariant::first();

        $taxBreakdown = new TaxBreakdown();
        $taxBreakdown->amounts = collect([]);

        $order = Order::create([
            'channel_id' => $channel->id,
            'user_id' => $user->id,
            'status' => 'awaiting-payment',
            'reference' => 'ORD-TEST-001',
            'customer_reference' => null,
            'sub_total' => 1000,
            'discount_total' => 0,
            'discount_breakdown' => null,
            'shipping_total' => 599,
            'shipping_breakdown' => null,
            'tax_total' => 0,
            'tax_breakdown' => $taxBreakdown,
            'total' => 1599,
            'notes' => 'Test order',
            'currency_code' => $currency->code,
            'compare_currency_code' => $currency->code,
            'exchange_rate' => 1,
            'placed_at' => now(),
            'meta' => [],
        ]);

        $this->assertDatabaseHas('lunar_orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'reference' => 'ORD-TEST-001',
        ]);

        // Test order can be loaded without errors
        $loadedOrder = Order::find($order->id);
        $this->assertEquals('ORD-TEST-001', $loadedOrder->reference);
        $this->assertEquals($user->id, $loadedOrder->user_id);
        $this->assertEquals(1599, $loadedOrder->total->value);
    }

    public function test_customer_can_only_see_their_own_orders(): void
    {
        $customer1 = User::factory()->create();
        $customer1->assignRole('customer');

        $customer2 = User::factory()->create();
        $customer2->assignRole('customer');

        $currency = Currency::first();
        $channel = Channel::first();
        $taxBreakdown = new TaxBreakdown();
        $taxBreakdown->amounts = collect([]);

        // Create order for customer 1
        Order::create([
            'channel_id' => $channel->id,
            'user_id' => $customer1->id,
            'status' => 'awaiting-payment',
            'reference' => 'ORD-CUSTOMER1',
            'sub_total' => 1000,
            'discount_total' => 0,
            'discount_breakdown' => null,
            'shipping_total' => 599,
            'shipping_breakdown' => null,
            'tax_total' => 0,
            'tax_breakdown' => $taxBreakdown,
            'total' => 1599,
            'currency_code' => $currency->code,
            'compare_currency_code' => $currency->code,
            'exchange_rate' => 1,
            'placed_at' => now(),
        ]);

        // Create order for customer 2
        Order::create([
            'channel_id' => $channel->id,
            'user_id' => $customer2->id,
            'status' => 'awaiting-payment',
            'reference' => 'ORD-CUSTOMER2',
            'sub_total' => 2000,
            'discount_total' => 0,
            'discount_breakdown' => null,
            'shipping_total' => 599,
            'shipping_breakdown' => null,
            'tax_total' => 0,
            'tax_breakdown' => $taxBreakdown,
            'total' => 2599,
            'currency_code' => $currency->code,
            'compare_currency_code' => $currency->code,
            'exchange_rate' => 1,
            'placed_at' => now(),
        ]);

        // Customer 1 should only see their order
        $this->actingAs($customer1);
        $response = $this->get('/my-orders');
        $response->assertStatus(200);
        $response->assertSee('ORD-CUSTOMER1');
        $response->assertDontSee('ORD-CUSTOMER2');
    }

    public function test_admin_can_access_lunar_admin_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);
        $response = $this->get('/lunar');
        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_lunar_admin_panel(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer);
        $response = $this->get('/lunar');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_dashboard(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer);
        $response = $this->get('/dashboard');
        $response->assertStatus(403);
    }
}

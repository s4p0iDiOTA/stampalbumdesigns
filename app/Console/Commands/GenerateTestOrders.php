<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lunar\Models\Order;
use Lunar\Models\OrderLine;
use Lunar\Models\OrderAddress;
use Lunar\Models\Currency;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Lunar\Base\ValueObjects\Cart\TaxBreakdown;
use Lunar\FieldTypes\Text;
use App\Models\PaperType;

class GenerateTestOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:generate-test {count=5 : Number of test orders to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test orders with realistic stamp album data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');

        $this->info("Generating {$count} test orders...");

        $currency = Currency::getDefault();
        $channel = Channel::getDefault();

        if (!$currency || !$channel) {
            $this->error('Default currency or channel not found. Please run lunar:install first.');
            return 1;
        }

        // Create default tax class
        $taxClass = TaxClass::firstOrCreate(
            ['name' => 'Default'],
            ['name' => 'Default', 'default' => true]
        );

        // Create a product type for stamp albums if it doesn't exist
        $productType = ProductType::firstOrCreate(
            ['name' => 'Stamp Album Pages'],
            ['name' => 'Stamp Album Pages']
        );

        // Create a generic test product and variant
        $product = Product::where('product_type_id', $productType->id)->first();

        if (!$product) {
            $product = new Product([
                'product_type_id' => $productType->id,
                'status' => 'published',
                'brand_id' => null,
            ]);
            $product->attribute_data = collect([
                'name' => new Text('Test Stamp Album Pages'),
            ]);
            $product->save();
        }

        // Create a variant for this product
        $variant = ProductVariant::firstOrCreate(
            ['product_id' => $product->id],
            [
                'product_id' => $product->id,
                'tax_class_id' => $taxClass->id,
                'sku' => 'STAMP-TEST-001',
                'stock' => 9999,
                'shippable' => true,
                'purchasable' => 'always',
            ]
        );

        // Create a price for the variant
        if ($variant->prices()->count() === 0) {
            $variant->prices()->create([
                'price' => 1000, // $10.00 in cents
                'currency_id' => $currency->id,
                'min_quantity' => 1,
            ]);
        }

        $countries = [
            'United States', 'Canada', 'United Kingdom', 'France', 'Germany',
            'Italy', 'Spain', 'Australia', 'Japan', 'Brazil', 'Mexico',
            'Argentina', 'Belgium', 'Netherlands', 'Switzerland'
        ];

        // Get paper types from centralized config
        $paperTypes = PaperType::all()->map(fn($type) => [
            'id' => $type->getId(),
            'name' => $type->getName(),
            'price' => $type->getPricePerPage(),
        ])->toArray();

        $statuses = ['awaiting-payment', 'payment-received', 'processing', 'shipped'];

        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Mary'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'Austin'];
        $states = ['NY', 'CA', 'TX', 'FL', 'IL', 'PA', 'OH', 'GA', 'NC', 'MI'];

        // Get US country for addresses
        $usCountry = Country::where('name', 'like', '%United States%')->first();

        // Get a user to assign orders to (prefer admin user)
        $user = \App\Models\User::where('email', 'admin@stampalbumdesigns.com')->first()
            ?? \App\Models\User::first();

        if (!$user) {
            $this->error('No users found. Please create a user first.');
            return 1;
        }

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        for ($i = 0; $i < $count; $i++) {
            // Random order details
            $itemCount = rand(1, 4);
            $subtotal = 0;
            $totalPages = 0;
            $orderGroups = [];

            // Generate random order items (matching /order page structure)
            for ($j = 0; $j < $itemCount; $j++) {
                $country = $countries[array_rand($countries)];
                $paperType = $paperTypes[array_rand($paperTypes)];
                $pages = rand(10, 200);
                $itemTotal = $pages * $paperType['price'];
                $subtotal += $itemTotal;
                $totalPages += $pages;

                $startYear = rand(1900, 2020);
                $endYear = rand(2021, 2024);

                $orderGroups[] = [
                    'id' => $j + 1,
                    'country' => $country,
                    'yearRange' => $startYear . ' - ' . $endYear,
                    'actualYearRange' => $startYear . '-' . $endYear,
                    'periods' => [
                        [
                            'id' => $j + 1,
                            'description' => 'Generated Period ' . ($j + 1),
                            'pages' => $pages,
                            'pagesInRange' => $pages,
                        ],
                    ],
                    'totalFiles' => 1,
                    'totalPages' => $pages,
                    'paperType' => (string)$paperType['price'], // Store as string like "0.20"
                    'expanded' => false,
                ];
            }

            $shippingCost = [5.99, 15.99, 29.99][array_rand([5.99, 15.99, 29.99])];
            $total = $subtotal + $shippingCost;

            // Create the order breakdowns
            $taxBreakdown = new TaxBreakdown();
            $taxBreakdown->amounts = collect([]);

            $order = Order::create([
                'channel_id' => $channel->id,
                'user_id' => $user->id, // Link order to user
                'status' => $statuses[array_rand($statuses)],
                'reference' => 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'customer_reference' => null,
                'sub_total' => (int)($subtotal * 100),
                'discount_total' => 0,
                'discount_breakdown' => null,
                'shipping_total' => (int)($shippingCost * 100),
                'shipping_breakdown' => null,
                'tax_total' => 0,
                'tax_breakdown' => $taxBreakdown,
                'total' => (int)($total * 100),
                'notes' => 'Test order generated via artisan command',
                'currency_code' => $currency->code,
                'compare_currency_code' => $currency->code,
                'exchange_rate' => 1,
                'placed_at' => now()->subDays(rand(0, 30)),
                'meta' => [
                    'payment_method' => ['credit_card', 'paypal'][array_rand(['credit_card', 'paypal'])],
                    'test_order' => true,
                ],
            ]);

            // Create addresses
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $city = $cities[array_rand($cities)];
            $state = $states[array_rand($states)];

            // Shipping address
            OrderAddress::create([
                'order_id' => $order->id,
                'country_id' => $usCountry?->id,
                'title' => null,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'company_name' => null,
                'line_one' => rand(100, 9999) . ' Main Street',
                'line_two' => rand(0, 1) ? 'Apt ' . rand(1, 999) : null,
                'line_three' => null,
                'city' => $city,
                'state' => $state,
                'postcode' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'delivery_instructions' => null,
                'contact_email' => strtolower($firstName . '.' . $lastName . '@example.com'),
                'contact_phone' => '555-' . rand(100, 999) . '-' . rand(1000, 9999),
                'type' => 'shipping',
                'shipping_option' => ['standard', 'express', 'overnight'][array_rand(['standard', 'express', 'overnight'])],
                'meta' => [],
            ]);

            // Billing address (same as shipping)
            OrderAddress::create([
                'order_id' => $order->id,
                'country_id' => $usCountry?->id,
                'title' => null,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'company_name' => null,
                'line_one' => rand(100, 9999) . ' Main Street',
                'line_two' => rand(0, 1) ? 'Apt ' . rand(1, 999) : null,
                'line_three' => null,
                'city' => $city,
                'state' => $state,
                'postcode' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'delivery_instructions' => null,
                'contact_email' => strtolower($firstName . '.' . $lastName . '@example.com'),
                'contact_phone' => '555-' . rand(100, 999) . '-' . rand(1000, 9999),
                'type' => 'billing',
                'shipping_option' => null,
                'meta' => [],
            ]);

            // Create ONE order line per order (matching /order page behavior)
            // The order line contains ALL order_groups in its meta field
            OrderLine::create([
                'order_id' => $order->id,
                'purchasable_type' => ProductVariant::class,
                'purchasable_id' => $variant->id,
                'type' => 'physical',
                'description' => "Stamp Album Pages - {$orderGroups[0]['country']}",
                'option' => null,
                'identifier' => $variant->sku,
                'unit_price' => (int)($subtotal * 100),
                'unit_quantity' => 1,
                'quantity' => 1,
                'sub_total' => (int)($subtotal * 100),
                'discount_total' => 0,
                'tax_total' => 0,
                'total' => (int)($subtotal * 100),
                'notes' => null,
                'tax_breakdown' => $taxBreakdown,
                'meta' => [
                    'order_groups' => $orderGroups,
                    'total_pages' => $totalPages,
                ],
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Successfully generated {$count} test orders!");
        $this->newLine();
        $this->line("View them at:");
        $this->line("  • Dashboard: /dashboard");
        $this->line("  • Lunar Admin: /lunar/orders");
        $this->newLine();

        // Show summary
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total') / 100;
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Orders', $totalOrders],
                ['Total Revenue', '$' . number_format($totalRevenue, 2)],
                ['Orders Created', $count],
            ]
        );

        return 0;
    }
}

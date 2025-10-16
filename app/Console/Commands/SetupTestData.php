<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SetupTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:test-data {--fresh : Clear existing test data first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up test admin user, customer user, and sample orders for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Setting up test data...');
        $this->newLine();

        // Check if we should clear existing data
        if ($this->option('fresh')) {
            $this->warn('⚠️  Clearing existing test data...');

            // Clear orders
            \Lunar\Models\OrderLine::truncate();
            \Lunar\Models\OrderAddress::truncate();
            \Lunar\Models\Order::truncate();

            // Delete test users
            User::where('email', 'admin@test.com')->delete();
            User::where('email', 'customer@test.com')->delete();

            $this->info('✅ Test data cleared');
            $this->newLine();
        }

        // Create admin user
        $this->info('👤 Creating admin user...');
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $this->info('✅ Admin user created');
        $this->line('   Email: admin@test.com');
        $this->line('   Password: password');
        $this->newLine();

        // Create customer user
        $this->info('👤 Creating customer user...');
        $customer = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$customer->hasRole('customer')) {
            $customer->assignRole('customer');
        }

        $this->info('✅ Customer user created');
        $this->line('   Email: customer@test.com');
        $this->line('   Password: password');
        $this->newLine();

        // Generate test orders for the customer
        $this->info('📦 Generating test orders for customer...');

        // Temporarily set the customer as the default user for order generation
        $orderCount = 3;

        // We'll manually create orders for the customer
        $this->call('orders:generate-test', ['count' => $orderCount]);

        // Update the generated orders to belong to the customer
        $orders = \Lunar\Models\Order::latest()->take($orderCount)->get();
        foreach ($orders as $order) {
            $order->update(['user_id' => $customer->id]);
        }

        $this->newLine();
        $this->info('✅ Test orders created for customer');
        $this->newLine();

        // Display summary
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🎉 Test data setup complete!');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $this->table(
            ['Role', 'Email', 'Password', 'Access'],
            [
                [
                    'Admin',
                    'admin@test.com',
                    'password',
                    '/dashboard, /lunar'
                ],
                [
                    'Customer',
                    'customer@test.com',
                    'password',
                    '/my-orders'
                ],
            ]
        );

        $this->newLine();
        $this->line('📋 What to test:');
        $this->line('');
        $this->line('  1. Login as admin@test.com:');
        $this->line('     • Visit /dashboard (should work)');
        $this->line('     • Visit /lunar/orders (should see all orders)');
        $this->line('     • View order details with JSON data');
        $this->line('');
        $this->line('  2. Login as customer@test.com:');
        $this->line('     • Visit /my-orders (should see only their ' . $orderCount . ' orders)');
        $this->line('     • Click on an order to see full details');
        $this->line('     • Verify order shows: countries, year ranges, pages, files, paper types');
        $this->line('     • Visit /dashboard (should get 403 Forbidden)');
        $this->line('');
        $this->line('  3. Test order creation:');
        $this->line('     • Visit /order (logged in as customer)');
        $this->line('     • Select paper type, country, year range, files');
        $this->line('     • Add to cart and checkout');
        $this->line('     • Verify order appears in /my-orders with all details');
        $this->line('');
        $this->newLine();

        return 0;
    }
}

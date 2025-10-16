<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ShowTestCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'show:test-credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show test user credentials and quick access info';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $customer = User::where('email', 'customer@test.com')->first();

        if (!$admin && !$customer) {
            $this->error('No test users found. Run: php artisan setup:test-data');
            return 1;
        }

        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🔐 Test User Credentials');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $rows = [];

        if ($admin) {
            $orderCount = \Lunar\Models\Order::where('user_id', $admin->id)->count();
            $rows[] = [
                '👑 Admin',
                'admin@test.com',
                'password',
                $admin->hasRole('admin') ? '✅' : '❌',
                $orderCount,
                '/dashboard, /lunar'
            ];
        }

        if ($customer) {
            $orderCount = \Lunar\Models\Order::where('user_id', $customer->id)->count();
            $rows[] = [
                '👤 Customer',
                'customer@test.com',
                'password',
                $customer->hasRole('customer') ? '✅' : '❌',
                $orderCount,
                '/my-orders'
            ];
        }

        $this->table(
            ['User', 'Email', 'Password', 'Role', 'Orders', 'Access'],
            $rows
        );

        $this->newLine();
        $this->line('🌐 Quick Links:');
        $this->line('  • Login: ' . config('app.url') . '/login');
        $this->line('  • Dashboard: ' . config('app.url') . '/dashboard');
        $this->line('  • Lunar Admin: ' . config('app.url') . '/lunar');
        $this->line('  • My Orders: ' . config('app.url') . '/my-orders');
        $this->line('  • Create Order: ' . config('app.url') . '/order');
        $this->newLine();

        $this->line('💡 Tips:');
        $this->line('  • To regenerate test data: php artisan setup:test-data --fresh');
        $this->line('  • To logout via URL: ' . config('app.url') . '/logout');
        $this->newLine();

        return 0;
    }
}

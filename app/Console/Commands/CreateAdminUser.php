<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {--email=} {--name=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user with full access to dashboard and Lunar admin panel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating admin user...');
        $this->newLine();

        // Get user input
        $name = $this->option('name') ?: $this->ask('Admin name');
        $email = $this->option('email') ?: $this->ask('Admin email');
        $password = $this->option('password') ?: $this->secret('Admin password (min 8 characters)');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  • ' . $error);
            }
            return 1;
        }

        // Check if admin role exists
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')
            ->where('guard_name', 'web')
            ->first();

        if (!$adminRole) {
            $this->error('Admin role not found. Please run migrations first: php artisan migrate');
            return 1;
        }

        // Create user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(), // Auto-verify admin users
        ]);

        // Assign admin role
        $user->assignRole('admin');

        $this->newLine();
        $this->info('✅ Admin user created successfully!');
        $this->newLine();
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', 'admin'],
                ['Email Verified', 'Yes'],
                ['Created At', $user->created_at->format('Y-m-d H:i:s')],
            ]
        );
        $this->newLine();
        $this->line('The user can now access:');
        $this->line('  • Dashboard: /dashboard');
        $this->line('  • Lunar Admin: /lunar');
        $this->newLine();

        return 0;
    }
}

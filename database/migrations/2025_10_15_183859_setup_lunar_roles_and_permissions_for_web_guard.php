<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $guard = 'web';

        // Create admin role for web guard
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => $guard,
        ]);

        // Create customer role for web guard (for regular users who can only view their own orders)
        $customerRole = Role::firstOrCreate([
            'name' => 'customer',
            'guard_name' => $guard,
        ]);

        // Define all Lunar admin permissions
        $permissions = [
            'settings',
            'settings:core',
            'settings:manage-staff',
            'settings:manage-attributes',
            'catalog:manage-products',
            'catalog:manage-collections',
            'sales:manage-orders',
            'sales:manage-customers',
            'sales:manage-discounts',
        ];

        // Create permissions for web guard
        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $createdPermissions[] = Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guard,
            ]);
        }

        // Assign all permissions to admin role
        $adminRole->syncPermissions($createdPermissions);

        // Customer role has no admin permissions (they'll only see their own orders)
        $customerRole->syncPermissions([]);

        // Assign admin role to existing admin user(s)
        // This ensures the role is assigned even if it already exists
        $adminUser = \App\Models\User::where('email', 'admin@stampalbumdesigns.com')->first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $guard = 'web';

        // Remove role assignments
        $adminRole = Role::where('name', 'admin')->where('guard_name', $guard)->first();
        $customerRole = Role::where('name', 'customer')->where('guard_name', $guard)->first();

        if ($adminRole) {
            $adminRole->delete();
        }

        if ($customerRole) {
            $customerRole->delete();
        }

        // Remove permissions for web guard
        Permission::where('guard_name', $guard)->delete();
    }
};

# Roles and Permissions System

## Overview

This application uses **Spatie Laravel Permission** (included with Lunar PHP) for role-based access control (RBAC). The system supports both admin users and regular customers with different levels of access.

**Important**: The Lunar Staff resource has been disabled since we use the standard Laravel `User` model instead of Lunar's `Staff` model. User management is handled through standard Laravel tools or can be added as a custom resource if needed.

## Roles

### 1. Admin Role
- **Name**: `admin`
- **Guard**: `web`
- **Access**:
  - Full access to Lunar admin panel at `/lunar`
  - Full access to main dashboard at `/dashboard`
  - Can manage products, collections, orders, customers, discounts
  - Can manage settings and attributes
  - Can manage other staff/admin users

### 2. Customer Role
- **Name**: `customer`
- **Guard**: `web`
- **Access**:
  - Can log in to the website
  - Can view their own orders
  - Cannot access `/lunar` admin panel
  - Cannot access `/dashboard` admin dashboard

### 3. Staff Role (Optional)
- **Name**: `staff`
- **Guard**: `web`
- **Access**: Created by Lunar's state management
- Currently unused but available for future mid-level admin access

## Permissions

The following permissions are available (all assigned to `admin` role):

### Settings Permissions
- `settings` - Access settings area
- `settings:core` - Manage core settings
- `settings:manage-staff` - Manage staff users
- `settings:manage-attributes` - Manage product attributes

### Catalog Permissions
- `catalog:manage-products` - Create, edit, delete products
- `catalog:manage-collections` - Manage product collections

### Sales Permissions
- `sales:manage-orders` - View and manage all orders
- `sales:manage-customers` - View and manage customer accounts
- `sales:manage-discounts` - Create and manage discounts

## Implementation Details

### User Model
The `User` model (app/Models/User.php) implements:
- `FilamentUser` - Enables Filament panel access
- `HasRoles` trait - Adds Spatie Permission functionality
- `TwoFactorAuthenticatable` - Enables 2FA for admin users

### Access Control
```php
// In User.php
public function canAccessPanel(Panel $panel): bool
{
    return $this->hasRole('admin');
}
```

Only users with the `admin` role can access the Lunar admin panel.

### Authentication Guard
Both the main app and Lunar admin use the same `web` guard, providing **single sign-on**:
- Log in once at `/login`
- Automatically authenticated for both `/dashboard` and `/lunar`
- Single logout logs out of both systems

## Database Tables

### Spatie Permission Tables
- `roles` - Stores role definitions
- `permissions` - Stores permission definitions
- `model_has_roles` - User-to-role assignments
- `model_has_permissions` - Direct permission assignments (rarely used)
- `role_has_permissions` - Role-to-permission assignments

### User Table Additions
- `two_factor_secret` - Stores 2FA secret (nullable)
- `two_factor_recovery_codes` - Stores recovery codes (nullable)
- `two_factor_confirmed_at` - Timestamp of 2FA setup (nullable)

## Migrations

### Migration 1: Setup Lunar Roles and Permissions
**File**: `2025_10_15_183859_setup_lunar_roles_and_permissions_for_web_guard.php`

Creates:
- `admin` role with all permissions
- `customer` role with no admin permissions
- All Lunar admin permissions for `web` guard
- Assigns `admin` role to `admin@stampalbumdesigns.com`

### Migration 2: Add Two-Factor Authentication Columns
**File**: `2025_10_15_184044_add_two_factor_columns_to_users_table.php`

Adds columns to `users` table:
- `two_factor_secret`
- `two_factor_recovery_codes`
- `two_factor_confirmed_at`

## Production Deployment

### Running Migrations
```bash
# On production server
php artisan migrate

# This will:
# 1. Create admin and customer roles for web guard
# 2. Create all necessary permissions
# 3. Add two-factor authentication columns to users table
# 4. Assign admin role to admin@stampalbumdesigns.com (if exists)
```

### Assigning Roles to Users

#### Via Artisan Tinker
```bash
php artisan tinker
```

```php
// Assign admin role to a user
$user = User::where('email', 'admin@example.com')->first();
$user->assignRole('admin');

// Assign customer role to a user
$user = User::where('email', 'customer@example.com')->first();
$user->assignRole('customer');

// Check user roles
$user->getRoleNames(); // Returns collection of role names
$user->hasRole('admin'); // Returns true/false
```

#### Via Code (e.g., in registration)
```php
// In your registration controller
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);

// Assign customer role by default
$user->assignRole('customer');
```

#### Via Database Seeder
```php
// database/seeders/AdminUserSeeder.php
$admin = User::create([
    'name' => 'Admin User',
    'email' => 'admin@stampalbumdesigns.com',
    'password' => Hash::make('secure-password'),
]);
$admin->assignRole('admin');
```

## Checking Permissions in Code

### In Controllers
```php
// Check if user has role
if (auth()->user()->hasRole('admin')) {
    // Admin-only logic
}

// Check if user has permission
if (auth()->user()->can('sales:manage-orders')) {
    // User can manage orders
}
```

### In Blade Templates
```blade
@role('admin')
    <a href="/lunar">Admin Panel</a>
@endrole

@can('sales:manage-orders')
    <a href="/lunar/orders">Manage Orders</a>
@endcan
```

### In Routes
```php
// Require admin role
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/reports', [ReportController::class, 'index']);
});

// Require specific permission
Route::middleware(['auth', 'permission:sales:manage-orders'])->group(function () {
    Route::get('/admin/orders', [OrderController::class, 'index']);
});
```

## Customer Order Access

For customers to view their own orders (without admin access):

### Create Controller
```php
// app/Http/Controllers/CustomerOrderController.php
class CustomerOrderController extends Controller
{
    public function index()
    {
        $orders = \Lunar\Models\Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = \Lunar\Models\Order::where('user_id', auth()->id())
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }
}
```

### Add Routes
```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{id}', [CustomerOrderController::class, 'show'])->name('orders.show');
});
```

## Security Best Practices

1. **Default Role Assignment**: Always assign the `customer` role to new registrations
2. **Admin Creation**: Only create admin users via seeder or manual database update, never through public registration
3. **Permission Checks**: Always check permissions before displaying sensitive UI or executing sensitive operations
4. **Role Changes**: Log all role assignments/removals for audit purposes
5. **Two-Factor Auth**: Encourage (or require) 2FA for admin users

## Troubleshooting

### User can't access /lunar
```bash
php artisan tinker
$user = User::find(1);
$user->hasRole('admin'); // Should return true
$user->assignRole('admin'); // If not
```

### Permission denied errors
```bash
# Clear permission cache
php artisan permission:cache-reset
php artisan config:clear
php artisan cache:clear
```

### Checking role assignments
```sql
-- Via SQLite
SELECT u.email, r.name as role
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN roles r ON mhr.role_id = r.id
WHERE r.guard_name = 'web';
```

## Managing Users

Since the Lunar Staff resource has been disabled, you can manage users in several ways:

### Option 1: Via Artisan Tinker (Quick)
```bash
php artisan tinker
$user = User::create([
    'name' => 'John Admin',
    'email' => 'john@example.com',
    'password' => Hash::make('password')
]);
$user->assignRole('admin');
```

### Option 2: Via Database Seeder (Recommended for Production)
```php
// database/seeders/AdminUserSeeder.php
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@stampalbumdesigns.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ]
        );
        $admin->assignRole('admin');
    }
}
```

### Option 3: Create Custom User Resource for Filament (Advanced)
If you want to manage users through the Lunar admin panel, you can create a custom Filament resource:

```bash
php artisan make:filament-resource User --generate
```

Then register it in your `AppServiceProvider`:
```php
LunarPanel::panel(function ($panel) {
    $resources = collect(\Lunar\Admin\LunarPanelManager::getResources())
        ->reject(fn ($resource) => $resource === \Lunar\Admin\Filament\Resources\StaffResource::class)
        ->push(\App\Filament\Resources\UserResource::class) // Add custom User resource
        ->values()
        ->toArray();

    return $panel->resources($resources);
})->register();
```

## Future Enhancements

Consider adding these role features:

1. **Manager Role**: Can manage products/orders but not settings
2. **Viewer Role**: Read-only access to admin panel
3. **Order Fulfillment Role**: Can only update order statuses
4. **Custom Permissions**: Add permission checks to your own controllers
5. **User Management UI**: Create a Filament resource for managing users through the admin panel

## References

- [Spatie Laravel Permission Docs](https://spatie.be/docs/laravel-permission)
- [Lunar PHP Documentation](https://docs.lunarphp.io)
- [Filament Admin Docs](https://filamentphp.com/docs)

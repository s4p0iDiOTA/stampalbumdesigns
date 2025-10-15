# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 11 application for stamp album designs, currently integrating **Lunar PHP** e-commerce framework.

### Current State (v1.0 - Pre-Lunar)
- User authentication and profile management (Laravel Breeze)
- Custom order builder with country and year selection
- Session-based shopping cart stored in database
- Checkout flow with shipping (no payment processing yet)
- Contact form system
- Admin user capability (basic authentication)

### Future State (v2.0 - Lunar Integration) 🚀
- Full e-commerce platform powered by Lunar PHP
- Filament admin dashboard with analytics
- Customer portal with order history
- Stripe payment integration
- Advanced order management and tracking
- Sales analytics and reporting
- PDF invoice generation

**📚 Important Documentation:**
- **LUNAR_INTEGRATION_PLAN.md** - Complete integration strategy and timeline
- **TODO.md** - Detailed implementation checklist with phases
- **README.md** - Project setup and features

## Development Commands

### Laravel/PHP Commands
- `php artisan serve` - Start the Laravel development server
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh --seed` - Reset database and run seeders
- `php artisan tinker` - Access Laravel's interactive shell
- `vendor/bin/pint` - Run Laravel Pint (PHP code formatter)
- `php artisan optimize:clear` - Clear all caches (config, route, view)

### Frontend Commands
- `npm run dev` - Start Vite development server with hot reload
- `npm run build` - Build frontend assets for production
- `npm run watch` - Watch for changes and rebuild

### Testing
- `php artisan test` - Run all tests using Pest PHP
- `php artisan test --filter=ExampleTest` - Run specific test
- `vendor/bin/pest` - Run Pest tests directly
- `php artisan test --coverage` - Run tests with coverage report

### Lunar Commands (Coming Soon)
- `php artisan lunar:install` - Install Lunar and create admin user
- `php artisan lunar:addons:discover` - Discover Lunar addons
- `composer require lunarphp/lunar` - Install Lunar core
- `composer require lunarphp/admin` - Install Lunar admin (Filament)
- `composer require lunarphp/stripe` - Install Stripe payment driver

## Architecture

### Current Architecture (v1.0)

#### Models and Relationships
- **User**: Standard Laravel user model with Breeze authentication
  - Location: `app/Models/User.php`
  - Has many: (future) Orders, Addresses

- **Country**: Represents stamp-issuing countries
  - Location: `app/Models/Country.php`
  - Has many: Periods

- **Period**: Time periods for stamp collections
  - Location: `app/Models/Period.php`
  - Belongs to: Country

#### Controllers
- **CartController**: Manages shopping cart operations
  - Location: `app/Http/Controllers/CartController.php`
  - Key methods: `addToCart()`, `cart()`, `updateCart()`, `removeFromCart()`, `clearCart()`
  - Uses: Session storage for cart data

- **CheckoutController**: Handles checkout process
  - Location: `app/Http/Controllers/CheckoutController.php`
  - Key methods: `index()`, `processCheckout()`, `confirmation()`, `getCart()`
  - Features: Shipping methods, address validation, order confirmation

- **CountryController**: Country search and listing
  - Location: `app/Http/Controllers/CountryController.php`
  - Methods: `search()`, `listCountryNames()`

- **ContactController**: Contact form management
  - Location: `app/Http/Controllers/ContactController.php`

- **ProfileController**: User profile management (Breeze)
  - Location: `app/Http/Controllers/ProfileController.php`

### Future Architecture (v2.0 - Lunar)

#### New Directory Structure
```
app/
├── Lunar/                          # Custom Lunar extensions
│   ├── Models/
│   │   ├── StampAlbumOrder.php    # Extended order model
│   │   └── StampAlbumProduct.php  # Custom product type
│   ├── Services/
│   │   ├── StampAlbumProductBuilder.php
│   │   └── OrderMigrationService.php
│   └── Pricing/
│       └── StampAlbumPricingManager.php
├── Filament/                       # Admin panel
│   ├── Resources/
│   │   ├── OrderResource.php
│   │   ├── CustomerResource.php
│   │   └── ProductResource.php
│   └── Widgets/
│       ├── RevenueStatsWidget.php
│       ├── SalesChartWidget.php
│       └── RecentOrdersWidget.php
└── Http/Controllers/
    └── Customer/                   # Customer portal
        ├── DashboardController.php
        ├── OrderHistoryController.php
        └── AccountController.php
```

#### Lunar Models (Auto-generated)
- **Lunar\Models\Product**: Product catalog
- **Lunar\Models\ProductVariant**: Product variations (paper types)
- **Lunar\Models\Order**: Order records with full history
- **Lunar\Models\OrderLine**: Individual order items
- **Lunar\Models\Cart**: Shopping cart (replaces session-based)
- **Lunar\Models\Customer**: Customer records (extends User)
- **Lunar\Models\Transaction**: Payment transactions
- **Lunar\Models\Address**: Shipping/billing addresses

### Frontend Stack

- **Blade Templates**: Laravel's templating engine
  - Layouts: `resources/views/components/layout.blade.php`
  - Order builder: `resources/views/order.blade.php`
  - Cart: `resources/views/cart/index.blade.php`
  - Checkout: `resources/views/checkout/index.blade.php`

- **Tailwind CSS**: Utility-first CSS framework
  - Config: `tailwind.config.js`
  - Custom styles: Inline in Blade files

- **Alpine.js**: Lightweight JavaScript framework
  - Used in: Order builder for reactive UI
  - Cart management
  - Dynamic pricing calculations

- **Pico CSS**: Minimal CSS framework for forms
  - Used for: Form styling, tables, buttons

- **Vite**: Frontend build tool
  - Config: `vite.config.js`
  - Entry point: `resources/js/app.js`

### Key Routes

#### Public Routes
- `/` - Homepage
- `/order` - Order builder (main feature) ⭐
- `/cart` - Shopping cart
- `/checkout` - Checkout page
- `/checkout/confirmation` - Order confirmation
- `/contact` - Contact form

#### Auth Routes (Laravel Breeze)
- `/login` - User login
- `/register` - User registration
- `/dashboard` - User dashboard (currently minimal)
- `/profile` - Profile edit

#### Checkout Routes
- `POST /cart` - Add to cart
- `GET /checkout` - Checkout page
- `POST /checkout/process` - Process order
- `GET /checkout/confirmation` - Order confirmation
- `DELETE /checkout/remove/{item_id}` - Remove cart item
- `GET /checkout/clear` - Clear cart

#### Future Routes (Lunar)
- `/admin` - Admin dashboard (Filament)
- `/admin/orders` - Order management
- `/admin/customers` - Customer management
- `/admin/products` - Product catalog
- `/account` - Customer dashboard
- `/account/orders` - Order history
- `/account/orders/{order}` - Order details

### Database

#### Current Tables (Laravel + Custom)
- `users` - User accounts with Breeze authentication
- `countries` - Country catalog (may be replaced by Lunar products)
- `periods` - Time periods for stamp collections
- `sessions` - Session storage (includes cart data)
- `cache` - Application cache
- `password_reset_tokens` - Password reset tokens
- `failed_jobs` - Failed queue jobs
- `migrations` - Migration history

#### Future Tables (Lunar - Auto-created)
Over 50+ tables will be added by Lunar, key ones include:
- `lunar_products` - Product catalog
- `lunar_product_variants` - Product variations
- `lunar_orders` - Order records
- `lunar_order_lines` - Order line items
- `lunar_carts` - Shopping carts
- `lunar_cart_lines` - Cart line items
- `lunar_customers` - Customer records
- `lunar_addresses` - Addresses
- `lunar_transactions` - Payment transactions
- `lunar_currencies` - Currency management
- `lunar_prices` - Product pricing
- `lunar_channels` - Sales channels
- And many more...

### Order Data Structure

#### Current Order Structure (Session-based)
```javascript
{
  "order_groups": [
    {
      "id": 1,
      "country": "CUBA",
      "yearRange": "1964 - 1970",
      "actualYearRange": "1964 - 1970",
      "periods": [
        {
          "id": 1,
          "description": "All Scott Listed",
          "pages": 8,
          "years": [1964, 1965, ...],
          "yearPageMap": {
            "1964": [2],
            "1965": [2],
            ...
          },
          "pagesInRange": 7
        }
      ],
      "totalFiles": 1,
      "totalPages": 7,
      "paperType": 0.20  // Price per page
    }
  ],
  "quantity": 1,
  "total": 1.40,
  "created_at": "2025-01-15 10:30:00"
}
```

#### Future Order Structure (Lunar)
Lunar will normalize this into:
- Order model with total, status, customer info
- OrderLine models for each country/paper type combination
- Custom meta fields for year ranges, files, page counts
- Transaction records for payments
- Address records for shipping

### Data Files

#### Country Data (JSON)
- `public/country_year_page_dict.json`
  - Structure: Country → Year → File → [Page Numbers]
  - Example: `"CUBA": {"1964": {"All Scott Listed": [2]}}`
  - Source of truth for available years and pages

- `public/page_count_per_file_per_country.json`
  - Structure: Country → File → Total Pages
  - Example: `"CUBA": {"All Scott Listed": 8}`
  - Used for total page counts

These will be migrated to Lunar product attributes during integration.

### Testing Framework

- **Pest PHP**: Modern testing framework
  - Config: `phpunit.xml` / `pest.php`
  - Tests location: `tests/Feature/` and `tests/Unit/`
  - Includes authentication tests and profile management tests

#### Test Categories
- Feature tests: Test full user flows (order, checkout, etc.)
- Unit tests: Test individual classes and methods
- Browser tests (future): Use Laravel Dusk for E2E testing

## Development Notes

### Authentication
- Uses **Laravel Breeze** for authentication scaffolding
- Email verification available but not required currently
- Standard password reset functionality
- Admin role will be managed via Lunar/Filament permissions

### Current Order Builder (`/order`)
**How it works:**
1. User selects paper type (Step 1)
2. User searches and selects country (Step 2)
3. System loads years from JSON data
4. User adjusts year range with sliders (Step 3)
5. System filters files and calculates pages based on year selection
6. User selects files (Step 4)
7. User clicks "Add to Order" - items go to right column summary
8. User can add multiple countries/paper types
9. Right column groups by paper type → country
10. User clicks "Add to Cart" → redirects to checkout

**Technical Details:**
- Built with Alpine.js for reactivity
- Uses `x-data="orderPageData()"` for state management
- Fetches JSON files on page load
- Calculates pages using Set operations for uniqueness
- Stores order in session via POST to `/cart`

### Cart System (Current)
- **Session-based** shopping cart stored in database sessions table
- Cart structure includes order groups with all metadata
- Persists across browser sessions
- Will be migrated to Lunar Cart models

### Cart System (Future with Lunar)
- Lunar Cart model with database persistence
- CartLine models for each item
- Support for guests and authenticated users
- Automatic cart merging on login
- Cart abandonment tracking

### Frontend Development Best Practices
- Use Blade components for reusable UI elements
- Keep JavaScript in Alpine.js components when possible
- Use Tailwind utilities instead of custom CSS
- Follow Pico CSS conventions for forms
- Maintain consistent styling across pages

### Lunar Integration Guidelines

**When adding Lunar features:**
1. **Keep existing UI** - Don't break current order builder
2. **Migrate gradually** - Phase by phase approach
3. **Extend, don't replace** - Use Lunar's extensibility
4. **Custom models** - Extend Lunar models for stamp-specific logic
5. **Admin interface** - Use Filament resources, customize as needed
6. **Testing** - Write tests for all custom Lunar extensions

**Custom Lunar Services to Create:**
- `StampAlbumProductBuilder` - Convert order groups to Lunar products
- `StampAlbumPricingManager` - Calculate pricing per page
- `OrderMigrationService` - Migrate session orders to Lunar
- `CountryImportService` - Import countries as products

### Stripe Integration

**Configuration (in .env):**
```env
STRIPE_KEY=pk_test_...           # Publishable key
STRIPE_SECRET=sk_test_...        # Secret key
STRIPE_WEBHOOK_SECRET=whsec_...  # Webhook signing secret
```

**Payment Flow:**
1. User completes checkout form
2. Frontend creates Stripe PaymentIntent
3. User enters card details in Stripe Elements
4. Stripe processes payment
5. Webhook confirms payment → create Lunar order
6. Redirect to confirmation page

**Test Cards:**
- Success: 4242 4242 4242 4242
- Decline: 4000 0000 0000 0002
- 3D Secure: 4000 0025 0000 3155

### Security Considerations

**Current:**
- CSRF protection on all forms
- Session security with database storage
- Password hashing with bcrypt
- XSS protection via Blade escaping

**Future (Lunar):**
- PCI compliance via Stripe Elements (never store cards)
- Webhook signature verification
- Role-based access control (admin/customer)
- Order authorization checks
- Secure payment processing

### Performance Optimization

**Current:**
- Database queries optimized with eager loading
- Session caching
- Asset compilation with Vite

**Future:**
- Lunar query optimization
- Redis caching for products and carts
- Queue jobs for emails and webhooks
- CDN for assets
- Database indexing for Lunar tables

### Common Tasks

#### Create Admin User
```php
php artisan tinker
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = bcrypt('password');
$user->email_verified_at = now();
$user->save();
```

#### Clear Session Cart
```php
php artisan tinker
session()->forget('cart');
```

#### Check Lunar Installation (Future)
```bash
php artisan lunar:status
```

#### Import Countries as Products (Future)
```bash
php artisan import:countries
```

### Troubleshooting

**"Undefined array key 'total'" Error**
- Old cart structure in session
- Solution: Clear session or add safety checks `isset($item['total'])`

**Session not persisting**
- Check `SESSION_DRIVER` in .env (should be `database`)
- Run migrations to create sessions table
- Clear config cache: `php artisan config:clear`

**Alpine.js not working**
- Check script is loaded: `<script src="//unpkg.com/alpinejs" defer></script>`
- Check for JavaScript errors in console
- Verify `x-data` is properly initialized

**Lunar migration conflicts (Future)**
- Review migration files in `vendor/lunarphp/`
- Create custom migrations if needed
- Use `--force` flag if necessary (dev only)

## Important Files Reference

### Configuration
- `.env` - Environment variables
- `config/lunar.php` - Lunar configuration (future)
- `config/database.php` - Database configuration
- `config/session.php` - Session configuration

### Core Application
- `routes/web.php` - All web routes
- `app/Http/Controllers/` - Controller files
- `app/Models/` - Eloquent models
- `app/Lunar/` - Custom Lunar extensions (future)

### Frontend
- `resources/views/` - Blade templates
- `resources/js/app.js` - JavaScript entry point
- `resources/css/app.css` - CSS entry point
- `public/` - Public assets and JSON data

### Documentation
- `LUNAR_INTEGRATION_PLAN.md` - **READ THIS for Lunar integration**
- `TODO.md` - Implementation checklist
- `README.md` - Project setup guide
- `CLAUDE.md` - This file

### Testing
- `tests/Feature/` - Feature tests
- `tests/Unit/` - Unit tests
- `phpunit.xml` - PHPUnit configuration

## Quick Reference Commands

```bash
# Start fresh development session
php artisan optimize:clear
php artisan migrate
npm run dev
php artisan serve

# Deploy/Production
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build

# Testing
php artisan test
vendor/bin/pint

# Database
php artisan migrate:fresh --seed
php artisan tinker

# Lunar (Future)
php artisan lunar:install
php artisan import:countries
composer require lunarphp/lunar
```

---

**Current Version:** 1.0.0 (Pre-Lunar)
**Laravel Version:** 11.45.1
**PHP Version:** 8.4.1
**Next Phase:** Lunar PHP Integration - See TODO.md
**Last Updated:** 2025-01-15

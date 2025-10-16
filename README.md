# Stamp Album Designs

A Laravel-based e-commerce platform for custom stamp album page orders, powered by Lunar PHP.

## 🚀 Features

### Current Features (✅ Live)
- **Custom Order Builder** - Interactive tool to build stamp album orders
  - Select paper type (5 options, $0.20-$0.35/page)
  - Search and select countries from JSON data
  - Filter by year ranges
  - Select specific files and pages
  - Real-time page count calculations
- **E-Commerce Platform** - Powered by Lunar PHP v1.1.0
- **Admin Dashboard** - Filament-based admin panel at `/lunar`
- **Customer Portal** - Order history and account management at `/my-orders`
- **Order Management** - Complete order tracking with detailed JSON data
- **Role-Based Access Control** - Admin and Customer roles with Spatie Permission
- **Session-Based Cart** - Persistent shopping cart
- **Checkout System** - Complete checkout flow with shipping and payment options

### Upcoming Features
- **Stripe Payments** - Secure payment processing integration
- **PDF Invoices** - Automatic invoice generation
- **Email Notifications** - Order confirmations and updates
- **Product Catalog** - Full product management system

## 📋 Requirements

- PHP 8.2 or higher (Currently running 8.4.1)
- Composer
- Laravel 11.x
- Lunar PHP v1.1.0
- SQLite or MySQL database
- Node.js & NPM (for frontend assets)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd stampalbumdesigns
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=sqlite
# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=stampalbumdesigns
# DB_USERNAME=root
# DB_PASSWORD=
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Set Up Test Data (Recommended for Development)
```bash
# Quick setup: Creates admin user, customer user, and test orders
php artisan setup:test-data --fresh
```

**Or create users manually:**

```bash
# Create admin user interactively
php artisan user:create-admin

# Or provide credentials directly
php artisan user:create-admin --name="Admin" --email="admin@example.com" --password="password"
```

**Test Credentials** (after running setup:test-data):
- **Admin:** admin@test.com / password
- **Customer:** customer@test.com / password

### 7. Build Frontend Assets
```bash
npm run dev  # For development
npm run build  # For production
```

### 8. Start Development Server
```bash
php artisan serve
```

Visit: http://localhost:8000

### 9. Access Admin Panel
After setting up test data, you can access:
- **Lunar Admin:** http://localhost:8000/lunar (admin@test.com)
- **Customer Orders:** http://localhost:8000/my-orders (customer@test.com)
- **Dashboard:** http://localhost:8000/dashboard (admin only)

## 📂 Project Structure

```
stampalbumdesigns/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── CreateAdminUser.php         # Create admin users
│   │       ├── GenerateTestOrders.php      # Generate test orders
│   │       ├── SetupTestData.php          # Quick test setup
│   │       └── ShowTestCredentials.php     # Show test login info
│   ├── Filament/
│   │   └── Resources/
│   │       └── UserResource.php           # User management in Lunar
│   ├── Http/
│   │   └── Controllers/
│   │       ├── CartController.php          # Shopping cart
│   │       ├── CheckoutController.php      # Checkout + Lunar orders
│   │       ├── CustomerOrderController.php # Customer order viewing
│   │       ├── CountryController.php       # Country search
│   │       └── ContactController.php       # Contact form
│   ├── Models/
│   │   ├── User.php                        # User with roles
│   │   ├── Country.php
│   │   └── Period.php
│   └── Providers/
│       └── AppServiceProvider.php         # Lunar configuration
├── database/
│   ├── migrations/
│   │   ├── 2025_10_15_183859_setup_lunar_roles_and_permissions_for_web_guard.php
│   │   ├── 2025_10_15_184044_add_two_factor_columns_to_users_table.php
│   │   └── 2025_10_15_184655_ensure_lunar_core_data_exists.php
│   └── database.sqlite
├── public/
│   ├── country_year_page_dict.json         # Country/year/page mappings (SOURCE OF TRUTH)
│   └── page_count_per_file_per_country.json # Page count data (SOURCE OF TRUTH)
├── resources/
│   ├── views/
│   │   ├── home.blade.php                  # Homepage
│   │   ├── order.blade.php                 # Order builder (JSON-based)
│   │   ├── orders/
│   │   │   ├── index.blade.php             # Customer order list
│   │   │   └── show.blade.php              # Order details with JSON data
│   │   ├── cart/
│   │   │   └── index.blade.php             # Shopping cart
│   │   ├── checkout/
│   │   │   ├── index.blade.php             # Checkout page
│   │   │   └── confirmation.blade.php      # Order confirmation
│   │   └── contact.blade.php               # Contact form
│   └── js/
│       └── app.js
├── routes/
│   └── web.php                             # All routes with role-based access
├── tests/
│   └── Feature/
│       ├── OrderFlowFromJsonTest.php      # JSON order flow tests
│       ├── OrderDataCaptureTest.php       # Order data verification
│       └── LunarIntegrationTest.php       # Lunar integration tests
└── CLAUDE.md                                # Development guidelines
```

## 🔑 Key Routes

### Public Routes
- `/` - Homepage
- `/order` - Order builder (JSON-based, main feature)
- `/cart` - Shopping cart
- `/checkout` - Checkout page
- `/contact` - Contact form

### Auth Routes (Laravel Breeze)
- `/login` - User login
- `/register` - User registration
- `/logout` - Logout (GET for convenience)

### Customer Routes (Authenticated)
- `/my-orders` - View your orders with full JSON data details
- `/my-orders/{order}` - Order detail page

### Admin Routes (Admin Role Required)
- `/dashboard` - Admin dashboard
- `/lunar` - Lunar admin panel (Filament)
- `/lunar/orders` - Order management with full details
- `/lunar/users` - User management

## 🎨 Frontend Stack

- **Blade Templates** - Laravel's templating engine
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Pico CSS** - Minimal CSS framework for forms
- **Vite** - Frontend build tool

## 🗄️ Database

### Core Tables
- `users` - User accounts with roles (admin, customer)
- `countries` - Country catalog
- `periods` - Time periods for stamp collections
- `sessions` - Session storage (includes cart data)
- `cache` - Cache storage
- `password_reset_tokens` - Password resets
- `failed_jobs` - Failed queue jobs
- `roles` & `permissions` - Spatie Permission tables

### Lunar Tables (✅ Active)
- `lunar_orders` - Order records with JSON metadata
- `lunar_order_lines` - Order line items with order_groups data
- `lunar_order_addresses` - Shipping and billing addresses
- `lunar_products` - Product catalog
- `lunar_product_variants` - Product variants
- `lunar_currencies` - Currency settings (USD default)
- `lunar_channels` - Sales channels (webstore)
- `lunar_countries` - Country data for addresses
- `lunar_languages` - Language settings
- `lunar_customer_groups` - Customer segmentation
- `lunar_tax_classes` - Tax configuration
- And 60+ more Lunar tables...

## 🧪 Testing

### Running Tests
```bash
# Run all tests (46 tests, 188 assertions)
php artisan test

# Run specific test suite
php artisan test --filter=OrderFlowFromJsonTest
php artisan test --filter=LunarIntegrationTest
php artisan test --filter=OrderDataCaptureTest

# Run tests with coverage
php artisan test --coverage
```

### Test Coverage
- ✅ **Authentication Tests** - Login, registration, password reset
- ✅ **Order Creation Tests** - Checkout process validation
- ✅ **Order Data Capture Tests** - Verifies all JSON data is saved
- ✅ **JSON Order Flow Tests** - End-to-end order flow from /order page
- ✅ **Lunar Integration Tests** - Access control and order management
- ✅ **Profile Tests** - User profile management

**Current Status:** 46 passing tests (188 assertions)

## 📝 Development Commands

### Quick Setup Commands
```bash
# Set up everything for testing (recommended!)
php artisan setup:test-data --fresh

# View test user credentials anytime
php artisan show:test-credentials

# Generate additional test orders
php artisan orders:generate-test 5
```

### User Management
```bash
# Create admin user (interactive)
php artisan user:create-admin

# Create admin user (non-interactive)
php artisan user:create-admin --name="Admin" --email="admin@example.com" --password="password"
```

### Laravel Commands
```bash
php artisan serve               # Start development server
php artisan migrate            # Run migrations
php artisan migrate:fresh      # Fresh migration (WARNING: Deletes all data)
php artisan tinker             # Interactive shell
php artisan optimize:clear     # Clear all caches
```

### Frontend Commands
```bash
npm run dev                    # Start Vite dev server
npm run build                  # Build for production
npm run watch                  # Watch for changes
```

### Code Quality
```bash
vendor/bin/pint                # Format PHP code
vendor/bin/pest                # Run Pest tests
```

## 🚀 Lunar PHP Integration

**Lunar PHP v1.1.0** is fully integrated and operational!

### Why Lunar PHP?

1. **Native Laravel Integration** - Built for Laravel 11+
2. **Flexible Product System** - Perfect for custom stamp album configurations
3. **Powerful Admin Interface** - Filament-based admin panel
4. **Stripe Integration** - Ready for secure payment processing
5. **Extensible Architecture** - Easy to customize for our needs

### Integration Status (Phase 1 Complete ✅)

- [x] **Phase 1: Lunar Core Installation** ✅
  - [x] Lunar PHP v1.1.0 installed
  - [x] Single sign-on authentication (web guard)
  - [x] Role-based access control (admin/customer)
  - [x] Order management system
  - [x] Admin panel at `/lunar`
  - [x] Customer portal at `/my-orders`
  - [x] Comprehensive test coverage (46 tests)

### Order Data Structure

Orders capture complete JSON-based data from the order builder:
```json
{
  "order_groups": [
    {
      "country": "ARGENTINA",
      "yearRange": "1847 - 2024",
      "actualYearRange": "1847-2024",
      "periods": [
        {
          "description": "Thru 1940",
          "pages": 71
        }
      ],
      "totalFiles": 2,
      "totalPages": 112,
      "paperType": "0.20"
    }
  ],
  "total_pages": 199
}
```

### Next Steps (Phase 2)
- [ ] Stripe payment integration
- [ ] Email notifications
- [ ] PDF invoice generation
- [ ] Advanced product catalog

## 🔐 Environment Variables

### Required
```env
APP_NAME=StampAlbumDesigns
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
```

### Optional (Coming with Lunar)
```env
# Stripe (for payments)
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@stampalbumdesigns.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 📚 Documentation

### Core Documentation
- **README.md** - This file (setup and usage)
- **CLAUDE.md** - Development guidelines for AI assistance
- **DEPLOYMENT_CHECKLIST.md** - Complete production deployment guide
- **QUICK_DEPLOYMENT_GUIDE.md** - Quick 30-minute deployment steps

### Key Files
- **public/page_count_per_file_per_country.json** - SOURCE OF TRUTH for stamp data
- **public/country_year_page_dict.json** - Year to page mappings
- **tests/Feature/** - Comprehensive test suite

### Guides (Coming Soon)
- Admin User Guide
- Customer User Guide
- API Documentation

## 🤝 Contributing

This is a private project. If you have access and want to contribute:

1. Create a feature branch
2. Make your changes
3. Write tests
4. Submit a pull request

## 📞 Support

- **Documentation:** See files in `/docs` directory
- **Issues:** Contact the development team
- **Lunar PHP:** https://docs.lunarphp.io
- **Laravel:** https://laravel.com/docs

## 📄 License

Proprietary - All rights reserved

## 🙏 Acknowledgments

- **Laravel Framework** - https://laravel.com
- **Lunar PHP** - https://lunarphp.io
- **Filament** - https://filamentphp.com
- **Tailwind CSS** - https://tailwindcss.com
- **Alpine.js** - https://alpinejs.dev

---

## 🎯 Quick Test Checklist

After running `php artisan setup:test-data --fresh`:

**Test as Admin (admin@test.com / password):**
- [ ] Login at `/login`
- [ ] Access `/dashboard` ✅
- [ ] Access `/lunar/orders` ✅
- [ ] View order details with JSON data ✅
- [ ] Manage users at `/lunar/users` ✅

**Test as Customer (customer@test.com / password):**
- [ ] Login at `/login`
- [ ] Access `/my-orders` ✅
- [ ] View 3 test orders ✅
- [ ] Click order to see details with countries, pages, files ✅
- [ ] Try `/dashboard` → Get 403 Forbidden ✅

**Test Order Creation:**
- [ ] Visit `/order` as customer
- [ ] Select paper type → country → year range → files
- [ ] Add to cart
- [ ] Complete checkout
- [ ] Verify order at `/my-orders` ✅

---

**Version:** 2.0.0 (Lunar Integration Phase 1 Complete)
**Last Updated:** 2025-01-16
**Next Milestone:** Stripe Payment Integration (Phase 2)

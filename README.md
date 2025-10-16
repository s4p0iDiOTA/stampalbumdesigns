# Stamp Album Designs

A Laravel-based e-commerce platform for custom stamp album page orders, powered by Lunar PHP.

## 🚀 Features

### Current Features
- **Custom Order Builder** - Interactive tool to build stamp album orders
  - Select paper type (5 options, $0.20-$0.35/page)
  - Search and select countries
  - Filter by year ranges
  - Select specific files and pages
- **Session-Based Cart** - Persistent shopping cart stored in database
- **Checkout System** - Complete checkout flow with shipping and payment options
- **Order Management** - Track orders through completion

### Upcoming Features (Lunar Integration)
- **Full E-Commerce Platform** - Powered by Lunar PHP
- **Admin Dashboard** - Comprehensive order and customer management
- **Customer Portal** - Account management and order history
- **Stripe Payments** - Secure payment processing
- **PDF Invoices** - Automatic invoice generation
- **Email Notifications** - Order confirmations and updates

## 📋 Requirements

- PHP 8.2 or higher (Currently running 8.4.1)
- Composer
- Laravel 11.x
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

### 6. Create Admin User
```bash
php artisan tinker
```
Then run:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@stampalbumdesigns.com';
$user->password = bcrypt('your-secure-password');
$user->email_verified_at = now();
$user->save();
```

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

## 📂 Project Structure

```
stampalbumdesigns/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── CartController.php          # Shopping cart
│   │       ├── CheckoutController.php      # Checkout process
│   │       ├── CountryController.php       # Country search
│   │       └── ContactController.php       # Contact form
│   ├── Models/
│   │   ├── User.php
│   │   ├── Country.php
│   │   └── Period.php
│   └── Lunar/                              # Coming soon: Lunar extensions
├── database/
│   ├── migrations/
│   └── database.sqlite
├── public/
│   ├── country_year_page_dict.json         # Country/year/page mappings
│   └── page_count_per_file_per_country.json # Page count data
├── resources/
│   ├── views/
│   │   ├── home.blade.php                  # Homepage
│   │   ├── order.blade.php                 # Order builder
│   │   ├── cart/
│   │   │   └── index.blade.php             # Shopping cart
│   │   ├── checkout/
│   │   │   ├── index.blade.php             # Checkout page
│   │   │   └── confirmation.blade.php      # Order confirmation
│   │   └── contact.blade.php               # Contact form
│   └── js/
│       └── app.js
├── routes/
│   └── web.php
├── LUNAR_INTEGRATION_PLAN.md               # Lunar PHP integration plan
├── TODO.md                                  # Detailed implementation checklist
└── CLAUDE.md                                # Development guidelines
```

## 🔑 Key Routes

### Public Routes
- `/` - Homepage
- `/order` - Order builder (main feature)
- `/cart` - Shopping cart
- `/checkout` - Checkout page
- `/contact` - Contact form

### Auth Routes (Laravel Breeze)
- `/login` - User login
- `/register` - User registration
- `/dashboard` - User dashboard (requires auth)

### Admin Routes (Coming Soon)
- `/admin` - Admin dashboard (Filament)
- `/admin/orders` - Order management
- `/admin/customers` - Customer management
- `/admin/products` - Product catalog

## 🎨 Frontend Stack

- **Blade Templates** - Laravel's templating engine
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Pico CSS** - Minimal CSS framework for forms
- **Vite** - Frontend build tool

## 🗄️ Database

### Current Tables
- `users` - User accounts
- `countries` - Country catalog
- `periods` - Time periods for stamp collections
- `sessions` - Session storage (includes cart data)
- `cache` - Cache storage
- `password_reset_tokens` - Password resets
- `failed_jobs` - Failed queue jobs

### Coming Soon (Lunar Tables)
- `lunar_products` - Product catalog
- `lunar_orders` - Order records
- `lunar_customers` - Customer data
- `lunar_carts` - Shopping carts
- `lunar_transactions` - Payment records
- And more...

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=ExampleTest

# Run tests with coverage
php artisan test --coverage
```

## 📝 Development Commands

### Laravel Commands
```bash
php artisan serve               # Start development server
php artisan migrate            # Run migrations
php artisan migrate:fresh      # Fresh migration (WARNING: Deletes all data)
php artisan tinker             # Interactive shell
php artisan optimize:clear     # Clear all caches

  # Interactive mode (will prompt for name, email, password)
  php artisan user:create-admin

  # Non-interactive mode (provide all options)
  php artisan user:create-admin --name="Admin Name" --email="admin@example.com" --password="password"
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

## 🚀 Lunar PHP Integration (In Progress)

We're integrating **Lunar PHP** - a modern Laravel e-commerce framework. See detailed plans:

- **Integration Plan:** `LUNAR_INTEGRATION_PLAN.md`
- **Implementation Checklist:** `TODO.md`

### Why Lunar PHP?

1. **Native Laravel Integration** - Built for Laravel 11+
2. **Flexible Product System** - Perfect for custom stamp album configurations
3. **Powerful Admin Interface** - Filament-based admin panel
4. **Stripe Integration** - Secure payment processing
5. **Extensible Architecture** - Easy to customize for our needs

### Integration Status

- [x] Planning phase completed
- [x] Documentation created
- [ ] Lunar installation (Week 1)
- [ ] Data migration (Week 2-3)
- [ ] Admin dashboard (Week 4-5)
- [ ] Customer portal (Week 5-6)
- [ ] Payment integration (Week 6-7)
- [ ] Testing & deployment (Week 7-8)

See `TODO.md` for detailed implementation checklist.

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

- **LUNAR_INTEGRATION_PLAN.md** - Complete integration strategy
- **TODO.md** - Detailed implementation checklist
- **CLAUDE.md** - Development guidelines for AI assistance
- **docs/** (Coming soon)
  - Admin Guide
  - Customer Guide
  - API Documentation
  - Deployment Guide

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

**Version:** 1.0.0 (Pre-Lunar Integration)
**Last Updated:** 2025-01-15
**Next Milestone:** Lunar PHP Integration - Week 1

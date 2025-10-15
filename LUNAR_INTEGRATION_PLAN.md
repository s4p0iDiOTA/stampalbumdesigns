# Lunar PHP Integration Plan - Stamp Album Designs

**Date Created:** 2025-01-15
**Current Laravel Version:** 11.45.1
**PHP Version:** 8.4.1
**Current Auth:** Laravel Breeze

## 📋 Executive Summary

This document outlines the complete plan to integrate Lunar PHP e-commerce framework into the Stamp Album Designs Laravel application. The integration will provide:

- Professional order management system
- Admin dashboard for sales tracking
- Customer portal for order history
- Stripe payment integration
- Complete audit trail of user actions

---

## 🎯 Project Goals

### Phase 1: Foundation (Week 1-2)
- [ ] Upgrade to Laravel 12 (when stable) or prepare for upgrade
- [ ] Install and configure Lunar PHP
- [ ] Set up Laravel starter kit (Breeze/Jetstream/Filament)
- [ ] Configure database structure for Lunar

### Phase 2: Order System Migration (Week 2-3)
- [ ] Map current custom order system to Lunar models
- [ ] Create product catalog for stamp albums
- [ ] Implement custom product variants (paper types, countries, years)
- [ ] Migrate checkout flow to Lunar

### Phase 3: Admin Dashboard (Week 3-4)
- [ ] Set up Lunar Admin panel
- [ ] Create custom reports for stamp album sales
- [ ] Implement order tracking and management
- [ ] Add customer activity monitoring
- [ ] Create analytics dashboard

### Phase 4: Customer Portal (Week 4-5)
- [ ] Build customer login/registration
- [ ] Create order history view
- [ ] Add order status tracking
- [ ] Implement PDF invoice generation
- [ ] Add reorder functionality

### Phase 5: Payment Integration (Week 5-6)
- [ ] Configure Stripe payment gateway
- [ ] Implement payment processing
- [ ] Add webhook handling
- [ ] Set up payment notifications

### Phase 6: Testing & Launch (Week 6-7)
- [ ] Comprehensive testing
- [ ] Data migration from current system
- [ ] User acceptance testing
- [ ] Production deployment

---

## 🏗️ Current System Analysis

### Existing Features
1. **Order Builder** (`/order`)
   - Paper type selection (5 types, $0.20-$0.35/page)
   - Country selection (from JSON data)
   - Year range filtering
   - File selection with page counts
   - Session-based cart

2. **Cart System**
   - Session storage in database
   - Order groups by paper type and country
   - Cart widget on order page
   - `/cart` page with item management

3. **Checkout Flow**
   - Order review
   - Shipping method selection (Standard, Express, Overnight)
   - Shipping address form
   - Payment method selection (UI only, not integrated)
   - Order confirmation

4. **Data Structure**
   - `country_year_page_dict.json` - Country → Year → File → Pages
   - `page_count_per_file_per_country.json` - Country → File → Total Pages
   - Session-based cart with order groups

### Current Controllers
- `CartController` - Cart management
- `CheckoutController` - Checkout process
- `CountryController` - Country search
- `ContactController` - Contact form

### Limitations to Address
- ❌ No payment processing
- ❌ No order persistence in database
- ❌ No admin panel
- ❌ No customer accounts/order history
- ❌ No inventory management
- ❌ No analytics/reporting

---

## 🚀 Lunar PHP Integration Architecture

### Why Lunar PHP?

1. **Modern Laravel Integration**
   - Built specifically for Laravel 11+
   - Follows Laravel conventions
   - Uses Eloquent models

2. **Flexible Product System**
   - Customizable product types
   - Variant support (perfect for paper types)
   - Dynamic pricing

3. **Powerful Admin Interface**
   - Pre-built admin panel (optional Filament integration)
   - Order management
   - Customer management
   - Reporting tools

4. **Payment Gateway Support**
   - Stripe integration out of the box
   - Extensible for other gateways
   - Webhook handling

5. **Customer Features**
   - User accounts
   - Order history
   - Address management
   - Saved payment methods

---

## 📦 Technical Implementation Plan

### 1. Laravel 12 Starter Kit Selection

**Recommended: Laravel Breeze with Livewire**
- Already have Breeze installed
- Add Livewire for reactive components
- Minimal overhead
- Easy to customize

**Alternative: Filament Admin Panel**
- Includes complete admin interface
- Form builder
- Table builder
- Perfect for order management

**Decision:** Start with Breeze + Livewire, add Filament Admin for backend

### 2. Lunar Installation

```bash
# Install Lunar Core
composer require lunarphp/lunar

# Install Lunar Admin (Filament-based)
composer require lunarphp/admin

# Install Stripe payment driver
composer require lunarphp/stripe

# Publish configuration
php artisan vendor:publish --tag=lunar

# Run migrations
php artisan migrate

# Create admin user
php artisan lunar:install
```

### 3. Database Structure

Lunar provides these key tables:
- `lunar_products` - Product catalog
- `lunar_product_variants` - Product variations (paper types)
- `lunar_orders` - Order records
- `lunar_order_lines` - Order line items
- `lunar_customers` - Customer records
- `lunar_carts` - Shopping carts
- `lunar_transactions` - Payment transactions
- `lunar_addresses` - Shipping/billing addresses

### 4. Custom Product Structure for Stamp Albums

```php
// Product Type: Stamp Album Pages
// Variants: Paper Type (Heavyweight, Scott International, etc.)
// Custom Attributes:
// - Country (JSON array)
// - Year Range (from-to)
// - File Descriptions (JSON array)
// - Total Pages (calculated)
```

### 5. Data Migration Strategy

**Step 1:** Create Lunar product categories
```php
- Category: "Stamp Album Pages"
  - Subcategories: By Country (optional)
```

**Step 2:** Create base products
```php
foreach ($countries as $country) {
    Product::create([
        'name' => "{$country} Stamp Album Pages",
        'product_type' => 'stamp_pages',
        'status' => 'published',
    ]);
}
```

**Step 3:** Create variants for paper types
```php
$paperTypes = [
    'heavyweight_3hole' => 0.20,
    'scott_international' => 0.30,
    'scott_specialized_2hole' => 0.35,
    'scott_specialized_3hole' => 0.35,
    'minkus_2hole' => 0.30,
];

foreach ($paperTypes as $type => $price) {
    ProductVariant::create([
        'product_id' => $product->id,
        'sku' => strtoupper($country . '_' . $type),
        'price' => $price * 100, // Lunar uses cents
    ]);
}
```

**Step 4:** Store custom data
```php
// Use Lunar's attribute system or JSON columns
$product->setAttribute('country_data', json_encode($countryData));
$product->setAttribute('year_page_mapping', json_encode($yearPageMapping));
```

---

## 🎨 Admin Dashboard Design

### Features Required

#### 1. Sales Dashboard (Home)
- Total revenue (today, week, month, year)
- Orders count
- Average order value
- Top selling countries
- Recent orders list
- Sales chart (line graph)

#### 2. Orders Management
- List all orders with filters:
  - Status (pending, processing, completed, cancelled)
  - Date range
  - Customer
  - Country
  - Payment status
- Order detail view:
  - Customer information
  - Order items (country, year range, pages, paper type)
  - Shipping address
  - Payment information
  - Status history
  - Action buttons (Mark as shipped, Cancel, Refund)

#### 3. Customer Management
- Customer list
- Customer detail:
  - Order history
  - Total spent
  - Average order value
  - Contact information
  - Address book

#### 4. Products Management
- Product catalog
- Add/edit countries
- Manage paper types and pricing
- Bulk updates

#### 5. Reports
- Sales by country
- Sales by paper type
- Customer lifetime value
- Revenue trends
- Export to CSV/Excel

#### 6. Settings
- Shipping methods and costs
- Tax configuration
- Payment gateway settings
- Email templates

---

## 👤 Customer Portal Design

### Features Required

#### 1. Customer Dashboard
- Welcome message
- Order summary cards
- Quick reorder
- Account settings link

#### 2. Order History
- List of all orders
- Filter by status, date
- Search orders

#### 3. Order Details
- Full order information
- Download invoice (PDF)
- Track shipment (if integrated with carrier)
- Reorder button

#### 4. Account Settings
- Profile information
- Change password
- Email preferences
- Address book

#### 5. Saved Items
- Save order configurations for later
- Quick reorder from saved items

---

## 🔧 Custom Lunar Extensions Needed

### 1. Custom Order Builder Model

```php
namespace App\Lunar\Models;

use Lunar\Models\Order;

class StampAlbumOrder extends Order
{
    protected $casts = [
        'order_groups' => 'array',
        'country_selections' => 'array',
        'year_ranges' => 'array',
    ];

    public function getCountriesAttribute()
    {
        return collect($this->order_groups)->pluck('country')->unique();
    }

    public function getTotalPagesAttribute()
    {
        return collect($this->order_groups)->sum('totalPages');
    }
}
```

### 2. Custom Product Builder

```php
namespace App\Services;

class StampAlbumProductBuilder
{
    public function buildFromOrderGroup(array $orderGroup)
    {
        // Convert current order group structure to Lunar cart items
        $product = Product::where('name', $orderGroup['country'])->first();
        $variant = $product->variants()
            ->where('paper_type', $orderGroup['paperType'])
            ->first();

        return [
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant->id,
            'quantity' => 1,
            'meta' => [
                'year_range' => $orderGroup['actualYearRange'],
                'files' => $orderGroup['periods'],
                'total_pages' => $orderGroup['totalPages'],
            ],
        ];
    }
}
```

### 3. Custom Pricing Calculator

```php
namespace App\Lunar\Pricing;

use Lunar\Base\PricingManagerInterface;

class StampAlbumPricingManager implements PricingManagerInterface
{
    public function for(Purchasable $purchasable)
    {
        // Custom pricing logic based on page count
        $pages = $purchasable->meta['total_pages'] ?? 0;
        $pricePerPage = $purchasable->variant->price;

        return new Price(
            value: $pages * $pricePerPage,
            currency: Currency::getDefault(),
        );
    }
}
```

---

## 🔌 Stripe Integration

### Setup Steps

1. **Install Stripe Package**
```bash
composer require lunarphp/stripe
```

2. **Configure Stripe Keys** (`.env`)
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

3. **Set Up Webhooks**
```php
// routes/webhooks.php
Route::post('stripe/webhook', [\Lunar\Stripe\Http\Controllers\WebhookController::class, 'handleWebhook']);
```

4. **Payment Flow**
```php
// Create payment intent
$order = Order::find($orderId);
$paymentIntent = $order->createPaymentIntent([
    'automatic_payment_methods' => ['enabled' => true],
]);

// Return client secret to frontend
return response()->json([
    'clientSecret' => $paymentIntent->client_secret,
]);
```

5. **Frontend Integration** (Use Stripe.js)
```javascript
// Existing checkout page, add Stripe Elements
const stripe = Stripe('{{ config('services.stripe.key') }}');
const elements = stripe.elements({ clientSecret });
const paymentElement = elements.create('payment');
paymentElement.mount('#payment-element');
```

---

## 📝 Implementation Checklist

### Pre-Implementation
- [x] Analyze current system
- [x] Create integration plan
- [ ] Review Lunar documentation thoroughly
- [ ] Set up development environment
- [ ] Create database backup

### Week 1: Foundation Setup
- [ ] Update composer.json with Lunar packages
- [ ] Run `composer update`
- [ ] Install Filament Admin: `composer require filament/filament`
- [ ] Publish Lunar config: `php artisan vendor:publish --tag=lunar`
- [ ] Run Lunar migrations: `php artisan migrate`
- [ ] Create admin user: `php artisan lunar:install`
- [ ] Configure Lunar settings

### Week 2: Data Structure
- [ ] Create custom product type for stamp albums
- [ ] Define product attributes (country, year range, files)
- [ ] Create migration for custom fields
- [ ] Build data import script for countries
- [ ] Create products for each country
- [ ] Create variants for paper types
- [ ] Test product catalog

### Week 3: Order System
- [ ] Create custom order builder service
- [ ] Integrate current `/order` page with Lunar cart
- [ ] Update cart system to use Lunar models
- [ ] Modify checkout flow for Lunar
- [ ] Implement order creation
- [ ] Test complete order flow

### Week 4: Admin Dashboard
- [ ] Install Filament: `composer require filament/filament`
- [ ] Create admin panel: `php artisan filament:install --panels`
- [ ] Register Lunar resources with Filament
- [ ] Create custom dashboard widgets:
  - [ ] Revenue stats
  - [ ] Order count
  - [ ] Sales chart
  - [ ] Recent orders
- [ ] Create custom order resource
- [ ] Add order status management
- [ ] Create customer resource
- [ ] Build reports section

### Week 5: Customer Portal
- [ ] Create customer dashboard route
- [ ] Build order history view
- [ ] Create order detail page
- [ ] Add PDF invoice generation
- [ ] Implement reorder functionality
- [ ] Create account settings page
- [ ] Add address management

### Week 6: Payment Integration
- [ ] Install Stripe package: `composer require lunarphp/stripe`
- [ ] Configure Stripe keys
- [ ] Set up webhook endpoint
- [ ] Update checkout page with Stripe Elements
- [ ] Implement payment intent creation
- [ ] Add payment confirmation handling
- [ ] Test payment flow (use test mode)
- [ ] Configure webhook events:
  - [ ] payment_intent.succeeded
  - [ ] payment_intent.payment_failed
  - [ ] charge.refunded

### Week 7: Testing & Polish
- [ ] Write feature tests
- [ ] Test all user flows
- [ ] Test admin functions
- [ ] Security audit
- [ ] Performance optimization
- [ ] Documentation updates
- [ ] User training materials

### Week 8: Deployment
- [ ] Data migration script
- [ ] Deploy to staging
- [ ] UAT testing
- [ ] Fix any issues
- [ ] Deploy to production
- [ ] Monitor for issues
- [ ] Create runbook

---

## 🗂️ File Structure Changes

### New Directories
```
app/
├── Lunar/
│   ├── Models/
│   │   ├── StampAlbumOrder.php
│   │   └── StampAlbumProduct.php
│   ├── Services/
│   │   ├── StampAlbumProductBuilder.php
│   │   └── OrderMigrationService.php
│   └── Pricing/
│       └── StampAlbumPricingManager.php
├── Filament/
│   ├── Resources/
│   │   ├── OrderResource.php
│   │   ├── CustomerResource.php
│   │   └── ProductResource.php
│   └── Widgets/
│       ├── RevenueStatsWidget.php
│       ├── SalesChartWidget.php
│       └── RecentOrdersWidget.php
└── Http/
    └── Controllers/
        └── Customer/
            ├── DashboardController.php
            ├── OrderHistoryController.php
            └── AccountController.php

resources/views/
├── customer/
│   ├── dashboard.blade.php
│   ├── orders/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── account/
│       ├── settings.blade.php
│       └── addresses.blade.php
└── admin/ (Filament handles most of this)

database/migrations/
├── xxxx_add_lunar_custom_fields.php
├── xxxx_create_stamp_album_product_type.php
└── xxxx_migrate_existing_orders.php
```

---

## 📚 Documentation Updates Required

### 1. README.md Updates
- [ ] Add Lunar PHP section
- [ ] Update installation instructions
- [ ] Add admin panel access info
- [ ] Document environment variables
- [ ] Add troubleshooting section

### 2. CLAUDE.md Updates
- [ ] Document Lunar models and relationships
- [ ] Add custom services documentation
- [ ] Update development commands
- [ ] Add testing guidelines for Lunar features

### 3. New Documentation Files
- [ ] `docs/ADMIN_GUIDE.md` - Admin panel usage
- [ ] `docs/CUSTOMER_GUIDE.md` - Customer portal features
- [ ] `docs/API.md` - API endpoints (if exposing)
- [ ] `docs/DEPLOYMENT.md` - Production deployment guide
- [ ] `docs/DATA_MIGRATION.md` - Migration procedures

---

## ⚠️ Risks & Mitigation

### Risk 1: Data Migration Complexity
**Impact:** High
**Probability:** Medium
**Mitigation:**
- Create comprehensive migration scripts
- Test on copy of production data
- Have rollback plan
- Migrate in phases

### Risk 2: Learning Curve for Lunar
**Impact:** Medium
**Probability:** High
**Mitigation:**
- Study Lunar documentation thoroughly
- Build prototype first
- Join Lunar Discord community
- Allocate extra time for learning

### Risk 3: Custom Requirements Don't Fit Lunar Model
**Impact:** High
**Probability:** Low
**Mitigation:**
- Verify Lunar flexibility early
- Design custom extensions
- Have fallback plan
- Consult Lunar team if needed

### Risk 4: Stripe Integration Issues
**Impact:** High
**Probability:** Low
**Mitigation:**
- Use Stripe test mode extensively
- Follow Lunar Stripe documentation
- Test webhook handling thoroughly
- Have support plan with Stripe

---

## 💰 Cost Considerations

### Software/Services
- Lunar PHP: **Free** (open source)
- Laravel: **Free**
- Filament Admin: **Free** (or $99/year for Pro)
- Stripe: 2.9% + $0.30 per transaction
- SSL Certificate: **Free** (Let's Encrypt) or $50-200/year
- Hosting: Varies ($20-200/month depending on traffic)

### Development Time
- Estimated: 8 weeks at 40 hours/week = 320 hours
- Additional polish/fixes: +40 hours
- Total: ~360 hours

---

## 📖 Resources

### Documentation
- Lunar Core: https://docs.lunarphp.io/core/overview.html
- Lunar Admin: https://docs.lunarphp.io/admin/overview.html
- Laravel 11: https://laravel.com/docs/11.x
- Filament: https://filamentphp.com/docs
- Stripe Laravel: https://stripe.com/docs/payments/accept-a-payment

### Community
- Lunar Discord: https://discord.gg/lunar
- Laravel Discord: https://discord.gg/laravel
- Filament Discord: https://discord.gg/filamentphp

### Learning Resources
- Lunar Crash Course: https://www.youtube.com/lunarphp
- Filament Admin Tutorial: https://www.youtube.com/filamentphp
- Stripe Payment Integration: https://stripe.com/docs/stripe-js

---

## 🎯 Success Criteria

### Must Have (MVP)
- ✅ Orders stored in database
- ✅ Admin can view/manage orders
- ✅ Stripe payment processing works
- ✅ Customers can create accounts
- ✅ Customers can view order history
- ✅ Email confirmations sent

### Should Have
- ✅ PDF invoice generation
- ✅ Sales analytics dashboard
- ✅ Customer activity tracking
- ✅ Shipping status updates
- ✅ Reorder functionality

### Nice to Have
- ⭐ Advanced reporting
- ⭐ Export to accounting software
- ⭐ Abandoned cart recovery
- ⭐ Customer notifications (SMS)
- ⭐ Integration with shipping carriers

---

## 📞 Next Steps

1. **Review this plan** with stakeholders
2. **Approve scope and timeline**
3. **Set up project tracking** (Jira, Trello, etc.)
4. **Schedule kickoff meeting**
5. **Begin Week 1 tasks**

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
**Next Review:** Start of each phase

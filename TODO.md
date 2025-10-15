# Stamp Album Designs - TODO List

**Project:** Lunar PHP E-Commerce Integration
**Created:** 2025-01-15
**Status:** Planning Phase

---

## 🚀 Phase 1: Foundation Setup (Week 1-2)

### Environment & Dependencies
- [ ] **Update PHP if needed** (Currently 8.4.1, Lunar requires 8.2+)
  - Status: ✅ Compatible
  - Notes: PHP 8.4.1 is perfect

- [ ] **Prepare for Laravel 12** (Currently 11.45.1)
  - [ ] Review Laravel 12 upgrade guide when released
  - [ ] Create upgrade checklist
  - [ ] Test on development environment
  - Notes: Laravel 11 is compatible with Lunar, but plan for 12

- [ ] **Install Lunar Core**
  ```bash
  composer require lunarphp/lunar
  ```
  - [ ] Run installation
  - [ ] Publish configuration files
  - [ ] Review config/lunar.php
  - [ ] Run migrations

- [ ] **Install Lunar Admin (Filament)**
  ```bash
  composer require lunarphp/admin
  composer require filament/filament
  ```
  - [ ] Run Filament installation
  - [ ] Create admin panel
  - [ ] Configure admin routes
  - [ ] Set up admin user permissions

- [ ] **Install Additional Packages**
  ```bash
  composer require lunarphp/stripe      # Stripe integration
  composer require livewire/livewire    # For reactive components
  composer require barryvdh/laravel-dompdf  # PDF generation
  ```

### Database Configuration
- [ ] **Backup Current Database**
  ```bash
  php artisan db:backup
  ```
  - [ ] Create backup directory
  - [ ] Test restore procedure

- [ ] **Review Lunar Migrations**
  - [ ] Check for conflicts with existing tables
  - [ ] Plan custom fields needed
  - [ ] Document migration strategy

- [ ] **Run Lunar Migrations**
  ```bash
  php artisan migrate
  ```
  - [ ] Verify all tables created
  - [ ] Check indexes
  - [ ] Review relationships

- [ ] **Create Custom Migrations**
  - [ ] Add stamp album specific fields
  - [ ] Create country_data custom table if needed
  - [ ] Migration for year_page_mappings

---

## 📦 Phase 2: Product & Data Structure (Week 2-3)

### Product Catalog Setup
- [ ] **Design Product Type: "Stamp Album Pages"**
  - [ ] Define required attributes
  - [ ] Plan variant structure (paper types)
  - [ ] Design pricing strategy

- [ ] **Create Product Categories**
  ```php
  Category::create(['name' => 'Stamp Album Pages', 'slug' => 'stamp-pages']);
  ```
  - [ ] Main category
  - [ ] Optional subcategories by region

- [ ] **Import Country Data**
  - [ ] Create seeder for countries
  - [ ] Parse existing country_year_page_dict.json
  - [ ] Parse page_count_per_file_per_country.json
  - [ ] Create products for each country

- [ ] **Create Paper Type Variants**
  - [ ] Heavyweight 3-hole ($0.20/page)
  - [ ] Scott International ($0.30/page)
  - [ ] Scott Specialized 2-hole ($0.35/page)
  - [ ] Scott Specialized 3-hole ($0.35/page)
  - [ ] Minkus 2-hole ($0.30/page)

- [ ] **Set Up Custom Attributes**
  - [ ] Country name
  - [ ] Available years (JSON array)
  - [ ] Year-to-page mapping (JSON)
  - [ ] File descriptions (JSON)
  - [ ] Total page count

- [ ] **Create Product Images** (Optional but recommended)
  - [ ] Design sample page images
  - [ ] Upload to storage
  - [ ] Associate with products

### Data Migration Scripts
- [ ] **Write Country Import Command**
  ```bash
  php artisan import:countries
  ```
  - [ ] Read JSON files
  - [ ] Create Lunar products
  - [ ] Create variants
  - [ ] Set attributes

- [ ] **Test Import on Small Dataset**
  - [ ] Test with 5-10 countries first
  - [ ] Verify data integrity
  - [ ] Check product display

- [ ] **Run Full Import**
  - [ ] Import all countries
  - [ ] Generate SKUs
  - [ ] Verify pricing

---

## 🛒 Phase 3: Order System Integration (Week 3-4)

### Cart System
- [ ] **Create Lunar Cart Integration Service**
  - [ ] File: `app/Lunar/Services/StampAlbumCartService.php`
  - [ ] Method: convertOrderGroupToCartLine()
  - [ ] Handle custom metadata (year ranges, files)

- [ ] **Update Order Page (`/order`)**
  - [ ] Keep existing UI
  - [ ] Replace session cart with Lunar cart
  - [ ] Update `addToCart()` JavaScript function
  - [ ] Test cart persistence

- [ ] **Update Cart Page (`/cart`)**
  - [ ] Fetch cart from Lunar models
  - [ ] Display Lunar cart items
  - [ ] Update quantity functionality
  - [ ] Remove items functionality

- [ ] **Update Checkout Page (`/checkout`)**
  - [ ] Replace session data with Lunar cart
  - [ ] Keep existing shipping/address forms
  - [ ] Prepare for Stripe integration

### Order Creation
- [ ] **Create Order from Cart**
  - [ ] Implement Lunar order creation
  - [ ] Store shipping information
  - [ ] Calculate totals correctly
  - [ ] Handle custom line item metadata

- [ ] **Order Confirmation**
  - [ ] Generate order number
  - [ ] Send confirmation email
  - [ ] Clear cart after order
  - [ ] Redirect to thank you page

- [ ] **Order Status Management**
  - [ ] Define order statuses:
    - [ ] Pending Payment
    - [ ] Processing
    - [ ] Awaiting Printing
    - [ ] Shipped
    - [ ] Completed
    - [ ] Cancelled
    - [ ] Refunded

---

## 👨‍💼 Phase 4: Admin Dashboard (Week 4-5)

### Filament Admin Setup
- [ ] **Configure Filament**
  ```bash
  php artisan filament:install --panels
  ```
  - [ ] Set admin route (e.g., /admin)
  - [ ] Configure authentication
  - [ ] Set up permissions

- [ ] **Create Admin User**
  ```bash
  php artisan make:filament-user
  ```
  - [ ] Email: admin@stampalbumdesigns.com
  - [ ] Strong password
  - [ ] Admin role

### Dashboard Widgets
- [ ] **Revenue Stats Widget**
  - [ ] Today's revenue
  - [ ] This week
  - [ ] This month
  - [ ] This year
  - [ ] Comparison with previous periods

- [ ] **Sales Chart Widget**
  - [ ] Line chart with daily sales
  - [ ] Configurable date range
  - [ ] Hover tooltips

- [ ] **Recent Orders Widget**
  - [ ] Last 10 orders
  - [ ] Quick actions (view, mark shipped)
  - [ ] Status indicators

- [ ] **Popular Countries Widget**
  - [ ] Top 10 countries by sales
  - [ ] Revenue per country
  - [ ] Order count

### Order Management
- [ ] **Order Resource**
  - [ ] List orders with filters:
    - [ ] Status
    - [ ] Date range
    - [ ] Customer
    - [ ] Country
  - [ ] Sortable columns
  - [ ] Bulk actions

- [ ] **Order Detail View**
  - [ ] Customer information
  - [ ] Shipping address
  - [ ] Order items with details
  - [ ] Payment information
  - [ ] Status history timeline
  - [ ] Action buttons:
    - [ ] Mark as Processing
    - [ ] Mark as Shipped
    - [ ] Cancel Order
    - [ ] Refund
    - [ ] Send Email
    - [ ] Print Invoice

- [ ] **Order Status Actions**
  - [ ] Update status
  - [ ] Add notes
  - [ ] Send customer notifications
  - [ ] Log all changes

### Customer Management
- [ ] **Customer Resource**
  - [ ] List all customers
  - [ ] Filter by registration date
  - [ ] Search by name/email

- [ ] **Customer Detail View**
  - [ ] Personal information
  - [ ] Order history
  - [ ] Lifetime value
  - [ ] Average order value
  - [ ] Last order date
  - [ ] Address book
  - [ ] Notes section

### Product Management
- [ ] **Product Resource** (Use Lunar's built-in)
  - [ ] Edit products
  - [ ] Manage variants
  - [ ] Update pricing
  - [ ] Stock management (if needed)

### Reports
- [ ] **Sales Report**
  - [ ] Revenue by date range
  - [ ] Filter by country
  - [ ] Filter by paper type
  - [ ] Export to CSV

- [ ] **Customer Report**
  - [ ] New customers by period
  - [ ] Repeat customer rate
  - [ ] Customer lifetime value
  - [ ] Export to CSV

- [ ] **Product Performance**
  - [ ] Most popular countries
  - [ ] Most popular paper types
  - [ ] Average pages per order
  - [ ] Revenue by product

---

## 👥 Phase 5: Customer Portal (Week 5-6)

### Customer Authentication
- [ ] **Use Existing Breeze Auth**
  - [ ] Already have login/registration
  - [ ] Add customer profile fields
  - [ ] Link to Lunar customer model

- [ ] **Customer Dashboard Route**
  ```php
  Route::middleware(['auth'])->group(function () {
      Route::get('/account', [CustomerDashboardController::class, 'index']);
      Route::get('/account/orders', [OrderHistoryController::class, 'index']);
      Route::get('/account/orders/{order}', [OrderHistoryController::class, 'show']);
  });
  ```

### Customer Dashboard
- [ ] **Dashboard View** (`resources/views/customer/dashboard.blade.php`)
  - [ ] Welcome message with name
  - [ ] Order summary cards:
    - [ ] Total orders
    - [ ] Pending orders
    - [ ] Total spent
  - [ ] Recent orders (last 5)
  - [ ] Quick links:
    - [ ] View all orders
    - [ ] Start new order
    - [ ] Account settings

### Order History
- [ ] **Order List View** (`resources/views/customer/orders/index.blade.php`)
  - [ ] Paginated list of orders
  - [ ] Order number
  - [ ] Date
  - [ ] Status badge
  - [ ] Total amount
  - [ ] Action buttons (View, Reorder)
  - [ ] Filter by status
  - [ ] Search by order number

- [ ] **Order Detail View** (`resources/views/customer/orders/show.blade.php`)
  - [ ] Order number and date
  - [ ] Status with timeline
  - [ ] Order items:
    - [ ] Country
    - [ ] Year range
    - [ ] Paper type
    - [ ] Pages
    - [ ] Price
  - [ ] Shipping information
  - [ ] Billing address
  - [ ] Payment method
  - [ ] Order total breakdown
  - [ ] Download invoice button
  - [ ] Reorder button
  - [ ] Contact support button

### Additional Features
- [ ] **PDF Invoice Generation**
  - [ ] Create invoice template
  - [ ] Include company logo
  - [ ] Order details
  - [ ] Line items with prices
  - [ ] Total breakdown
  - [ ] Download as PDF

- [ ] **Reorder Functionality**
  - [ ] Button on order detail page
  - [ ] Copy order items to new cart
  - [ ] Redirect to cart
  - [ ] Notification of items added

- [ ] **Account Settings**
  - [ ] Edit profile information
  - [ ] Change password
  - [ ] Email preferences
  - [ ] Manage saved addresses
  - [ ] Delete account option

---

## 💳 Phase 6: Payment Integration (Week 6-7)

### Stripe Setup
- [ ] **Create Stripe Account**
  - [ ] Sign up at stripe.com
  - [ ] Verify business information
  - [ ] Enable test mode

- [ ] **Configure Stripe in Laravel**
  ```env
  STRIPE_KEY=pk_test_...
  STRIPE_SECRET=sk_test_...
  STRIPE_WEBHOOK_SECRET=whsec_...
  ```
  - [ ] Add to .env
  - [ ] Update .env.example
  - [ ] Document in README

- [ ] **Install Lunar Stripe Driver**
  ```bash
  composer require lunarphp/stripe
  php artisan vendor:publish --tag=lunar.stripe
  ```

- [ ] **Configure Webhook Endpoint**
  - [ ] Create route: `/stripe/webhook`
  - [ ] Register in Stripe dashboard
  - [ ] Add webhook secret to .env
  - [ ] Test webhook locally (use Stripe CLI)

### Payment Integration
- [ ] **Update Checkout Page**
  - [ ] Add Stripe.js script
  - [ ] Create Stripe Elements container
  - [ ] Initialize payment element
  - [ ] Style to match site design

- [ ] **Create Payment Intent**
  - [ ] Generate on checkout page load
  - [ ] Pass client secret to frontend
  - [ ] Store intent ID with order

- [ ] **Handle Payment Submission**
  - [ ] Confirm payment on form submit
  - [ ] Handle success:
    - [ ] Create Lunar order
    - [ ] Mark as paid
    - [ ] Send confirmation email
    - [ ] Redirect to confirmation
  - [ ] Handle errors:
    - [ ] Display error message
    - [ ] Allow retry

- [ ] **Webhook Handling**
  - [ ] Handle `payment_intent.succeeded`
    - [ ] Update order status
    - [ ] Send confirmation
  - [ ] Handle `payment_intent.payment_failed`
    - [ ] Update order status
    - [ ] Notify customer
  - [ ] Handle `charge.refunded`
    - [ ] Update order status
    - [ ] Process refund in system

### Testing
- [ ] **Test Card Numbers** (Stripe test mode)
  - [ ] 4242 4242 4242 4242 - Success
  - [ ] 4000 0000 0000 0002 - Decline
  - [ ] 4000 0000 0000 9995 - Insufficient funds

- [ ] **Test Scenarios**
  - [ ] Successful payment
  - [ ] Declined card
  - [ ] 3D Secure authentication
  - [ ] Expired card
  - [ ] Network error
  - [ ] Webhook failure/retry

### Security
- [ ] **Verify Webhook Signatures**
  - [ ] Validate all webhook requests
  - [ ] Return 400 for invalid signatures

- [ ] **HTTPS Required**
  - [ ] Ensure production uses HTTPS
  - [ ] Update Stripe webhook URL

- [ ] **PCI Compliance**
  - [ ] Never store card numbers
  - [ ] Use Stripe Elements (handles PCI)
  - [ ] Review security checklist

---

## 🧪 Phase 7: Testing (Week 7-8)

### Unit Tests
- [ ] **Order Creation Tests**
  - [ ] Test order from cart conversion
  - [ ] Test custom metadata storage
  - [ ] Test price calculations

- [ ] **Product Tests**
  - [ ] Test variant creation
  - [ ] Test pricing per page
  - [ ] Test attribute storage

- [ ] **Cart Tests**
  - [ ] Test add to cart
  - [ ] Test update quantities
  - [ ] Test remove items
  - [ ] Test cart persistence

### Feature Tests
- [ ] **Customer Flow Tests**
  - [ ] Registration
  - [ ] Login
  - [ ] Build order
  - [ ] Checkout
  - [ ] View order history

- [ ] **Admin Flow Tests**
  - [ ] Login to admin
  - [ ] View dashboard
  - [ ] Manage orders
  - [ ] Update order status
  - [ ] View reports

- [ ] **Payment Tests**
  - [ ] Successful payment
  - [ ] Failed payment
  - [ ] Webhook processing
  - [ ] Refund processing

### Manual Testing
- [ ] **Cross-Browser Testing**
  - [ ] Chrome
  - [ ] Firefox
  - [ ] Safari
  - [ ] Edge

- [ ] **Mobile Testing**
  - [ ] iOS Safari
  - [ ] Android Chrome
  - [ ] Responsive design check

- [ ] **User Acceptance Testing**
  - [ ] Test with sample users
  - [ ] Gather feedback
  - [ ] Fix issues

---

## 📚 Phase 8: Documentation (Ongoing)

### Update Existing Docs
- [x] **LUNAR_INTEGRATION_PLAN.md**
  - Status: ✅ Created
  - Location: Project root

- [ ] **README.md**
  - [ ] Add Lunar PHP section
  - [ ] Update installation steps
  - [ ] Document admin access
  - [ ] Add development commands
  - [ ] Environment variables documentation

- [ ] **CLAUDE.md**
  - [ ] Add Lunar model relationships
  - [ ] Document custom services
  - [ ] Add admin panel info
  - [ ] Update architecture section
  - [ ] Add testing guidelines

### Create New Documentation
- [ ] **ADMIN_GUIDE.md**
  - [ ] How to access admin panel
  - [ ] Managing orders
  - [ ] Managing customers
  - [ ] Running reports
  - [ ] Common tasks
  - [ ] Troubleshooting

- [ ] **CUSTOMER_GUIDE.md**
  - [ ] How to create account
  - [ ] Building an order
  - [ ] Checkout process
  - [ ] Viewing order history
  - [ ] Downloading invoices
  - [ ] FAQ

- [ ] **DEPLOYMENT.md**
  - [ ] Server requirements
  - [ ] Installation steps
  - [ ] Environment configuration
  - [ ] Database migration
  - [ ] Stripe configuration
  - [ ] Monitoring setup
  - [ ] Backup procedures

- [ ] **DATA_MIGRATION.md**
  - [ ] Current system export
  - [ ] Lunar data import
  - [ ] Verification steps
  - [ ] Rollback procedures

- [ ] **API.md** (if building API)
  - [ ] Authentication
  - [ ] Endpoints
  - [ ] Request/response examples
  - [ ] Rate limiting
  - [ ] Error codes

### Code Documentation
- [ ] **Add PHPDoc Comments**
  - [ ] Controllers
  - [ ] Models
  - [ ] Services
  - [ ] Helpers

- [ ] **Generate API Documentation**
  ```bash
  php artisan ide-helper:generate
  php artisan ide-helper:models
  ```

---

## 🚀 Phase 9: Deployment (Week 8+)

### Pre-Deployment
- [ ] **Code Review**
  - [ ] Review all custom code
  - [ ] Security audit
  - [ ] Performance optimization
  - [ ] Remove debug code

- [ ] **Database Backup**
  - [ ] Full backup of current production DB
  - [ ] Test restore procedure
  - [ ] Document backup location

- [ ] **Environment Preparation**
  - [ ] Set up production .env
  - [ ] Configure production Stripe keys
  - [ ] Set APP_ENV=production
  - [ ] Set APP_DEBUG=false
  - [ ] Configure caching
  - [ ] Set up queue workers

### Deployment Steps
- [ ] **Deploy to Staging**
  - [ ] Run migrations
  - [ ] Import data
  - [ ] Run tests
  - [ ] UAT testing

- [ ] **Deploy to Production**
  - [ ] Enable maintenance mode
  - [ ] Pull latest code
  - [ ] Run `composer install --no-dev --optimize-autoloader`
  - [ ] Run migrations
  - [ ] Clear caches
  - [ ] Restart queue workers
  - [ ] Disable maintenance mode

- [ ] **Post-Deployment**
  - [ ] Verify site is accessible
  - [ ] Test order creation
  - [ ] Test payment processing
  - [ ] Test admin access
  - [ ] Monitor logs for errors
  - [ ] Test webhooks

### Monitoring
- [ ] **Set Up Error Tracking**
  - [ ] Install Sentry or Bugsnag
  - [ ] Configure error notifications

- [ ] **Set Up Uptime Monitoring**
  - [ ] Configure uptime monitoring service
  - [ ] Set up alerts

- [ ] **Set Up Performance Monitoring**
  - [ ] New Relic or Laravel Telescope
  - [ ] Monitor slow queries
  - [ ] Track response times

---

## 📋 Ongoing Maintenance

### Daily Tasks
- [ ] Monitor error logs
- [ ] Check pending orders
- [ ] Review failed payments

### Weekly Tasks
- [ ] Review sales reports
- [ ] Check customer support tickets
- [ ] Update inventory if needed
- [ ] Review site performance

### Monthly Tasks
- [ ] Database optimization
- [ ] Security updates
- [ ] Backup verification
- [ ] Analytics review
- [ ] Customer feedback review

---

## 🎯 Future Enhancements

### Phase 10: Advanced Features
- [ ] **Abandoned Cart Recovery**
  - [ ] Email reminders for abandoned carts
  - [ ] Discount codes for recovery

- [ ] **Shipping Integration**
  - [ ] USPS API integration
  - [ ] Real-time shipping rates
  - [ ] Tracking number updates

- [ ] **Advanced Analytics**
  - [ ] Google Analytics 4 integration
  - [ ] Conversion tracking
  - [ ] Customer behavior analysis

- [ ] **Marketing Features**
  - [ ] Email newsletter integration
  - [ ] Discount code system
  - [ ] Referral program
  - [ ] Loyalty points

- [ ] **Customer Features**
  - [ ] Wishlist functionality
  - [ ] Product reviews
  - [ ] Saved configurations
  - [ ] Gift options

- [ ] **Admin Features**
  - [ ] Advanced reporting
  - [ ] Export to QuickBooks
  - [ ] Bulk order processing
  - [ ] Custom email templates

---

## 📝 Notes

### Important Reminders
- Always test in development/staging first
- Keep backups before major changes
- Document all custom modifications
- Follow Laravel and Lunar best practices
- Keep dependencies updated

### Contacts
- Lunar Discord: https://discord.gg/lunar
- Laravel Discord: https://discord.gg/laravel
- Stripe Support: https://support.stripe.com

### Useful Commands
```bash
# Clear all caches
php artisan optimize:clear

# Run all tests
php artisan test

# Check Lunar status
php artisan lunar:status

# Import products
php artisan import:countries

# Generate admin user
php artisan make:filament-user
```

---

**Last Updated:** 2025-01-15
**Next Review:** Start of each phase

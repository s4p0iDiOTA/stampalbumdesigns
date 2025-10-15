# Testing Guide - Stamp Album Designs

**Last Updated:** October 15, 2025
**Test Framework:** PHPUnit
**Laravel Version:** 11.46.1
**Lunar PHP:** v1.1.0

---

## 📊 Current Test Status

### Test Summary
| Category | Total | Passing | Failing | Status |
|----------|-------|---------|---------|--------|
| **Lunar Integration** | 5 | 5 | 0 | ✅ 100% |
| **Unit Tests** | 1 | 1 | 0 | ✅ 100% |
| **Laravel Breeze Auth** | 19 | 0 | 19 | ❌ (Pest syntax) |
| **Profile Tests** | 5 | 0 | 5 | ❌ (Pest syntax) |
| **TOTAL** | 30 | 6 | 24 | 20% passing |

### Working Tests (PHPUnit Style)
✅ **tests/Feature/OrderCreationTest.php** - 5/5 passing
- `test_checkout_creates_lunar_order()` ✅
- `test_checkout_calculates_totals_correctly()` ✅
- `test_checkout_requires_cart()` ✅
- `test_checkout_validates_required_fields()` ✅
- `test_checkout_clears_cart_after_order()` ✅

✅ **tests/Unit/ExampleTest.php** - 1/1 passing
- `test_that_true_is_true()` ✅

### Pest-Syntax Tests (Not Running)
These tests use Pest function syntax but Pest.php was removed:
- ❌ tests/Feature/Auth/* (19 tests)
- ❌ tests/Feature/ProfileTest.php (5 tests)
- ❌ tests/Feature/ExampleTest.php (1 test)

---

## 🚀 How to Run Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test File
```bash
php artisan test --filter=OrderCreationTest
```

### Run With Coverage (if installed)
```bash
php artisan test --coverage
```

### Run With Detailed Output
```bash
php artisan test --testdox
```

### Run Specific Test Method
```bash
php artisan test --filter=test_checkout_creates_lunar_order
```

---

## ✅ Implemented Tests

### 1. Order Creation Tests

**File:** `tests/Feature/OrderCreationTest.php`
**Purpose:** Test Lunar PHP order creation integration
**Coverage:** CheckoutController, Lunar Models

#### Test Cases:

**Test 1: `test_checkout_creates_lunar_order()`**
- ✅ Verifies order is created in database
- ✅ Checks order status is 'awaiting-payment'
- ✅ Validates subtotal and shipping calculations
- ✅ Confirms 2 addresses created (shipping + billing)
- ✅ Verifies order line item description
- ✅ Checks redirect to confirmation page

**Test 2: `test_checkout_calculates_totals_correctly()`**
- ✅ Tests cart with multiple items
- ✅ Validates subtotal: (25 × 2) + 35 = $85.00
- ✅ Validates express shipping: $15.99
- ✅ Validates total: $100.99
- ✅ All monetary values stored as cents (multiplied by 100)

**Test 3: `test_checkout_requires_cart()`**
- ✅ Ensures empty cart redirects to /order
- ✅ Shows error message
- ✅ Prevents order creation without cart items

**Test 4: `test_checkout_validates_required_fields()`**
- ✅ Tests validation for all required checkout fields
- ✅ Validates: payment_method, shipping_name, address, city, state, zip, country
- ✅ Ensures no order created with invalid data

**Test 5: `test_checkout_clears_cart_after_order()`**
- ✅ Verifies cart session is cleared after successful order
- ✅ Confirms last_order session is set
- ✅ Ensures user can't accidentally re-submit

#### Test Setup

Each test automatically creates:
- ✅ Currency (USD)
- ✅ Channel (Webstore)
- ✅ Language (English)
- ✅ Country (United States)
- ✅ TaxClass (Default)
- ✅ ProductType (Stamp Album Pages)
- ✅ Product with attribute_data
- ✅ ProductVariant with SKU
- ✅ Price for variant

This ensures all Lunar dependencies exist for testing.

---

## 🔧 Test Data Generator

### Command: `php artisan orders:generate-test`

**Purpose:** Generate realistic test orders for manual testing and development

**Usage:**
```bash
# Generate 5 test orders (default)
php artisan orders:generate-test

# Generate specific number
php artisan orders:generate-test 10
php artisan orders:generate-test 50
```

**What It Creates:**
- ✅ Realistic customer names, addresses, cities
- ✅ Random order items with stamp album data
- ✅ Multiple countries and paper types
- ✅ Varied order statuses (awaiting-payment, processing, shipped, etc.)
- ✅ Random shipping methods
- ✅ Payment methods (credit_card, paypal)
- ✅ Proper Lunar relationships (Product, Variant, TaxClass)
- ✅ Linked to US country for addresses

**Output:**
```
✅ Successfully generated 10 test orders!

View them at:
  • Dashboard: /dashboard
  • Lunar Admin: /lunar/orders

+----------------+---------+
| Metric         | Value   |
+----------------+---------+
| Total Orders   | 21      |
| Total Revenue  | $1,841.65 |
| Orders Created | 10      |
+----------------+---------+
```

---

## 📋 Testing Recommendations

### Phase 1 Tests (✅ DONE)
- [x] Order creation from checkout
- [x] Cart validation
- [x] Field validation
- [x] Total calculations
- [x] Session management

### Recommended Additional Tests

#### High Priority
1. **Cart Operations Tests**
   ```php
   - test_can_add_item_to_cart()
   - test_can_update_cart_quantity()
   - test_can_remove_cart_item()
   - test_cart_persists_across_requests()
   ```

2. **Product Variant Tests**
   ```php
   - test_variant_has_correct_price()
   - test_variant_links_to_product()
   - test_variant_has_tax_class()
   ```

3. **Country/Address Tests**
   ```php
   - test_can_lookup_country_by_name()
   - test_address_links_to_country()
   - test_address_validation()
   ```

#### Medium Priority
4. **Tax Calculation Tests**
   ```php
   - test_tax_breakdown_created()
   - test_zero_tax_for_test_orders()
   - test_tax_calculation_with_rates()
   ```

5. **Shipping Tests**
   ```php
   - test_standard_shipping_cost()
   - test_express_shipping_cost()
   - test_overnight_shipping_cost()
   ```

6. **Order Status Tests**
   ```php
   - test_order_status_transitions()
   - test_cannot_cancel_shipped_order()
   - test_order_status_history()
   ```

#### Low Priority
7. **Admin Panel Tests**
   ```php
   - test_admin_can_view_orders()
   - test_admin_can_update_order_status()
   - test_admin_can_search_orders()
   ```

8. **Customer Integration Tests**
   ```php
   - test_guest_checkout()
   - test_authenticated_checkout()
   - test_order_appears_in_customer_history()
   ```

---

## 🧪 Test Database

### Configuration
- **Driver:** SQLite (in-memory for tests)
- **RefreshDatabase:** Used in OrderCreationTest
- **Migrations:** Run automatically before each test

### Key Points
- Tests use `RefreshDatabase` trait
- Database is reset between tests
- All Lunar migrations run automatically
- No test data persists to main database

---

## 🎯 Test Coverage Goals

### Current Coverage
```
Controllers:
  ✅ CheckoutController::processCheckout() - COVERED
  ❌ CheckoutController::index() - NOT COVERED
  ❌ CheckoutController::getCart() - NOT COVERED
  ❌ CheckoutController::removeCartItem() - NOT COVERED
  ❌ CheckoutController::clearCart() - NOT COVERED

Models:
  ⚠️  Lunar Models - Using vendor package tests

Services:
  ❌ No custom services yet (Phase 2)

Commands:
  ⚠️  GenerateTestOrders - Manually tested, no unit tests
```

### Target Coverage (Phase 2)
- Controllers: 80%
- Models: 70%
- Services: 90%
- Commands: 60%

---

## 🔍 Debugging Tests

### View Test Database
Tests use in-memory SQLite, but you can inspect it during tests:

```php
// In test method
dump(\Lunar\Models\Order::all());
dd(\Lunar\Models\Product::count());
```

### Common Issues

**Issue:** "Call to undefined function pest()"
**Solution:** tests/Pest.php was removed. Only use PHPUnit class-based tests.

**Issue:** "Cannot assign null to property... of type Language"
**Solution:** Create Language in test setUp():
```php
\Lunar\Models\Language::create([
    'code' => 'en',
    'name' => 'English',
    'default' => true,
]);
```

**Issue:** "table lunar_countries has no column named currency_name"
**Solution:** Use only fields that exist in schema (check with `.schema lunar_countries`)

**Issue:** "Target class [config] does not exist"
**Solution:** This is a Pest-syntax test. Convert to PHPUnit or skip.

---

## 📝 Writing New Tests

### Template for PHPUnit Test

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Lunar\Models\Order;
use Lunar\Models\Currency;
use Lunar\Models\Channel;
use Lunar\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;

class YourNewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required Lunar entities
        Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'exchange_rate' => 1.00,
            'decimal_places' => 2,
            'enabled' => true,
            'default' => true,
        ]);

        Channel::create([
            'name' => 'Webstore',
            'handle' => 'webstore',
            'default' => true,
            'url' => config('app.url'),
        ]);

        Language::create([
            'code' => 'en',
            'name' => 'English',
            'default' => true,
        ]);
    }

    public function test_your_feature(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->post('/your-route', [
            'field' => 'value',
        ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('your_table', [
            'field' => 'value',
        ]);
    }
}
```

### Best Practices

1. **Use Descriptive Names**
   - ✅ `test_checkout_creates_lunar_order()`
   - ❌ `test_order_creation()`

2. **Follow AAA Pattern**
   - Arrange: Set up test data
   - Act: Perform the action
   - Assert: Verify the result

3. **Test One Thing**
   - Each test should verify one specific behavior
   - Split complex scenarios into multiple tests

4. **Use Factories**
   ```php
   $user = User::factory()->create();
   ```

5. **Clean Assertions**
   ```php
   $this->assertEquals(5000, $order->sub_total->value);
   $this->assertCount(2, $order->addresses);
   $this->assertDatabaseHas('lunar_orders', ['status' => 'awaiting-payment']);
   ```

---

## 🚨 Test Maintenance

### When Adding New Features
1. Write tests BEFORE implementing feature (TDD)
2. Ensure tests cover happy path and edge cases
3. Run full test suite before committing
4. Update this documentation

### When Modifying Code
1. Run affected tests
2. Update tests if behavior changed intentionally
3. Ensure test coverage doesn't decrease

### CI/CD Integration
```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

---

## 📚 Resources

### Documentation
- **PHPUnit:** https://phpunit.de/documentation.html
- **Laravel Testing:** https://laravel.com/docs/11.x/testing
- **Lunar Testing:** https://docs.lunarphp.io/testing

### Internal Docs
- **PHASE1_COMPLETE.md** - Phase 1 completion summary
- **PHASE1_TESTING_STATUS.md** - Detailed testing status
- **TODO.md** - Task checklist with test items marked

---

## ✅ Quick Commands Reference

```bash
# Run all tests
php artisan test

# Run only OrderCreationTest
php artisan test --filter=OrderCreationTest

# Run with detailed output
php artisan test --testdox

# Generate test orders
php artisan orders:generate-test 10

# Check test count
find tests -name "*Test.php" | wc -l

# Clear test cache
php artisan test:clear-cache
```

---

**Status:** Phase 1 Testing Complete ✅
**Next Steps:** Add tests for Phase 2 features (Products, Variants, Catalog)

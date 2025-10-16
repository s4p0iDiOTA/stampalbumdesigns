# Endicia Shipping Integration - Implementation Summary

## What Was Created

### 1. Core Services

#### **ShippingCalculator.php** (`app/Services/`)
Handles conversion of cart items to physical shipping specifications:
- Converts page counts to weight based on paper type specifications
- Calculates package dimensions (length, width, height)
- Determines optimal packaging (envelope vs box)
- Provides detailed breakdowns for debugging/display

**Key Features:**
- Supports 4 paper types with different weights (0.20, 0.25, 0.30, 0.35 per page)
- Automatic package type selection based on thickness
- Includes packaging material weights (envelope/box, padding)
- Easy to adjust paper specifications

#### **EndiciaService.php** (`app/Services/`)
Integrates with Endicia Label Server SOAP API:
- Fetches real-time USPS shipping rates
- Supports multiple mail classes (Priority, Express, First-Class, Media Mail)
- Handles SOAP XML requests and responses
- Built-in error handling and logging
- Test connection method

**Key Features:**
- Multiple rate fetching in single request
- Automatic parsing of XML responses
- Fallback handling for API failures
- Delivery time estimation

### 2. Controller

#### **ShippingRateController.php** (`app/Http/Controllers/`)
Provides API endpoints for frontend integration:
- `POST /api/shipping/rates` - Get rates for address
- `GET /api/shipping/breakdown` - Get weight/dimension breakdown
- `GET /api/shipping/test` - Test API connection

**Features:**
- JSON responses for easy frontend integration
- Automatic fallback to static rates on API failure
- Session cart integration

### 3. Frontend Assets

#### **checkout-shipping.js** (`public/js/`)
Alpine.js component for dynamic rate loading:
- Auto-fetches rates when ZIP entered
- Loading states and error handling
- Calculates total with shipping
- Displays package details

### 4. Configuration

#### **services.php** (Enhanced)
Added Endicia configuration section:
```php
'endicia' => [
    'api_url' => env('ENDICIA_API_URL'),
    'account_id' => env('ENDICIA_ACCOUNT_ID'),
    'pass_phrase' => env('ENDICIA_PASS_PHRASE'),
    'from_zip' => env('ENDICIA_FROM_ZIP'),
    'test_mode' => env('ENDICIA_TEST_MODE', true),
]
```

### 5. Routes

#### **web.php** (Enhanced)
Added three new API routes:
```php
Route::post('/api/shipping/rates', [ShippingRateController::class, 'getRates']);
Route::get('/api/shipping/breakdown', [ShippingRateController::class, 'getBreakdown']);
Route::get('/api/shipping/test', [ShippingRateController::class, 'testConnection']);
```

### 6. Testing Command

#### **TestEndiciaIntegration.php** (`app/Console/Commands/`)
Artisan command for testing the integration:
```bash
php artisan endicia:test
php artisan endicia:test --zip=10001 --pages=100 --paper=0.30
```

**Tests:**
- Configuration verification
- Weight/dimension calculation
- API connectivity
- Live rate fetching

### 7. Documentation

#### **ENDICIA_INTEGRATION_GUIDE.md**
Comprehensive guide covering:
- Architecture overview
- Configuration instructions
- API endpoint documentation
- Usage examples
- Troubleshooting guide
- Production checklist
- Future enhancements

#### **.env.endicia.example**
Template for environment variables

---

## Quick Start Guide

### Step 1: Get Endicia Credentials

1. Sign up at https://www.endicia.com/
2. Request developer/API access
3. Get test credentials (Account ID and Pass Phrase)

### Step 2: Configure Environment

Add to `.env`:
```env
ENDICIA_API_URL=https://elstestserver.endicia.com/LabelService/EwsLabelService.asmx
ENDICIA_ACCOUNT_ID=your_account_id
ENDICIA_PASS_PHRASE=your_pass_phrase
ENDICIA_FROM_ZIP=90210
ENDICIA_TEST_MODE=true
```

### Step 3: Test Integration

```bash
# Clear config cache
php artisan config:clear

# Run test command
php artisan endicia:test

# Test with custom parameters
php artisan endicia:test --zip=10001 --pages=50 --paper=0.25
```

### Step 4: Integrate into Checkout

Add to your checkout view:
```blade
<script src="{{ asset('js/checkout-shipping.js') }}"></script>

<div x-data="checkoutWithShipping()">
    <input type="text" x-model="shippingZip" placeholder="ZIP Code">

    <div x-show="loadingRates">Calculating rates...</div>

    <template x-for="rate in shippingRates">
        <label>
            <input type="radio"
                   name="shipping_method"
                   :value="rate.service_code"
                   x-model="selectedShippingMethod">
            <span x-text="rate.service_name"></span>
            <span x-text="'$' + rate.cost.toFixed(2)"></span>
        </label>
    </template>
</div>
```

---

## Paper Specifications

Current configured paper types:

| Price/Page | Weight/Page | Thickness | Description |
|------------|-------------|-----------|-------------|
| $0.20 | 0.16 oz | 0.004" | Economy Paper (20lb bond) |
| $0.25 | 0.20 oz | 0.005" | Standard Paper (24lb bond) |
| $0.30 | 0.24 oz | 0.006" | Premium Paper (28lb bond) |
| $0.35 | 0.28 oz | 0.007" | Deluxe Paper (32lb bond) |

**To Adjust:**
1. Weigh actual samples
2. Measure with caliper
3. Edit `ShippingCalculator::PAPER_SPECS`

---

## Package Type Selection

The system automatically selects packaging:

**Envelope (< 0.75" thick):**
- Weight: +1.5 oz (envelope + padding)
- Standard envelope size: 12" × 10"
- Endicia type: `FlatRateEnvelope`

**Box (≥ 0.75" thick):**
- Weight: +4 oz (box)
- Standard box: 12" × 9" × 3"
- Endicia type: `Package`

---

## API Response Example

```json
{
  "success": true,
  "rates": [
    {
      "service_code": "first",
      "service_name": "USPS First-Class Mail",
      "cost": 5.99,
      "currency": "USD",
      "delivery_days": "2-5 business days",
      "provider": "USPS",
      "package_type": "envelope"
    },
    {
      "service_code": "priority",
      "service_name": "USPS Priority Mail",
      "cost": 9.45,
      "currency": "USD",
      "delivery_days": "2-3 business days",
      "provider": "USPS",
      "package_type": "envelope"
    }
  ],
  "breakdown": {
    "total_weight": {
      "weight_oz": 8.5,
      "weight_lbs": 0.53
    },
    "dimensions": {
      "length": 12,
      "width": 10,
      "height": 1,
      "type": "envelope"
    }
  }
}
```

---

## Integration with Current Checkout

The existing `CheckoutController` needs minor updates:

1. **Replace static shipping methods** with dynamic rates
2. **Store selected rate** in order meta
3. **Display package details** to customer

Example modification:
```php
// In CheckoutController::index()
// OLD:
$shippingMethods = [
    'standard' => ['name' => 'Standard', 'price' => 5.99],
];

// NEW: Pass empty array, rates loaded via AJAX
$shippingMethods = [];
```

---

## Testing Checklist

- [ ] Environment variables configured
- [ ] `php artisan endicia:test` passes
- [ ] Rates load in checkout when ZIP entered
- [ ] Different paper types calculate correctly
- [ ] Large orders (100+ pages) use box packaging
- [ ] Small orders (< 30 pages) use envelope
- [ ] Fallback rates work when API unavailable
- [ ] Shipping cost added to order total
- [ ] Selected rate saved with order

---

## Production Deployment

Before going live:

1. **Switch to production API:**
   ```env
   ENDICIA_API_URL=https://labelserver.endicia.com/LabelService/EwsLabelService.asmx
   ENDICIA_TEST_MODE=false
   ```

2. **Update credentials** to production account

3. **Set correct warehouse ZIP:**
   ```env
   ENDICIA_FROM_ZIP=your_actual_zip
   ```

4. **Verify paper specifications** with real samples

5. **Test with actual orders** before launch

6. **Set up monitoring** for API failures

7. **Configure fallback rates** appropriately

---

## Troubleshooting

### "No rates returned"
- Check API credentials
- Verify ZIP code format (5 digits)
- Check logs: `storage/logs/laravel.log`
- Test with `php artisan endicia:test`

### "Weight too low" error
- Endicia has minimum weights per service
- Ensure packaging weight is added
- Check paper specifications

### API connection fails
- Verify internet connectivity
- Check firewall/proxy settings
- Confirm API URL is correct
- Test mode vs production URL

### Incorrect shipping costs
- Verify paper weight specifications
- Check packaging weights
- Confirm origin ZIP code
- Test with known weight samples

---

## Next Steps

1. **Calibrate paper weights** - Weigh actual samples
2. **Test with real API** - Get Endicia account
3. **Update checkout UI** - Integrate dynamic rates
4. **Add rate caching** - Improve performance
5. **Monitor API usage** - Track quota
6. **Consider label generation** - Future enhancement

---

## Support

- **Documentation**: `ENDICIA_INTEGRATION_GUIDE.md`
- **Endicia API**: https://www.endicia.com/developer/docs/els.html
- **Test Command**: `php artisan endicia:test --help`
- **Logs**: `storage/logs/laravel.log`

---

**Version**: 1.0
**Created**: October 16, 2025
**Status**: Ready for Testing

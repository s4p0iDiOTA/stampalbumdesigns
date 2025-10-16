# Endicia Shipping Integration Guide

## Overview

This integration provides real-time USPS shipping rate calculations via the Endicia Label Server API. The system automatically:
- Converts page counts to weight based on paper type
- Calculates package dimensions
- Determines optimal packaging (envelope vs box)
- Fetches live USPS rates from Endicia
- Falls back to static rates if API is unavailable

## Architecture

### Services

1. **ShippingCalculator** (`app/Services/ShippingCalculator.php`)
   - Converts cart items to physical specifications
   - Calculates weight based on paper type
   - Determines package dimensions
   - Selects appropriate packaging type

2. **EndiciaService** (`app/Services/EndiciaService.php`)
   - Integrates with Endicia Label Server API
   - Fetches real-time USPS rates
   - Handles API communication and error handling
   - Parses SOAP XML responses

3. **ShippingRateController** (`app/Http/Controllers/ShippingRateController.php`)
   - Provides API endpoints for rate calculation
   - Handles AJAX requests from checkout page
   - Returns JSON responses with shipping options

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# Endicia Configuration
ENDICIA_API_URL=https://elstestserver.endicia.com/LabelService/EwsLabelService.asmx
ENDICIA_ACCOUNT_ID=your_account_id
ENDICIA_PASS_PHRASE=your_pass_phrase
ENDICIA_FROM_ZIP=90210
ENDICIA_TEST_MODE=true
```

### Getting Endicia Credentials

1. Sign up for Endicia account: https://www.endicia.com/
2. Request API access through developer portal
3. Get test credentials for development
4. Switch to production credentials for live site

### Paper Specifications

Paper types are configured in `ShippingCalculator.php`:

```php
private const PAPER_SPECS = [
    '0.20' => [
        'name' => 'Economy Paper',
        'weight_per_page_oz' => 0.16,  // 20lb bond paper
        'thickness_inches' => 0.004,
    ],
    '0.25' => [
        'name' => 'Standard Paper',
        'weight_per_page_oz' => 0.20,  // 24lb bond paper
        'thickness_inches' => 0.005,
    ],
    // ... more types
];
```

**To adjust these:**
1. Weigh sample pages of each paper type
2. Measure thickness with caliper
3. Update constants in `ShippingCalculator.php`

## API Endpoints

### Get Shipping Rates
```
POST /api/shipping/rates
Content-Type: application/json

{
  "zip": "10001",
  "state": "NY",
  "city": "New York"
}
```

**Response:**
```json
{
  "success": true,
  "rates": [
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
    },
    "paper_types": {
      "0.25": {
        "name": "Standard Paper",
        "pages": 30,
        "weight_oz": 6.0
      }
    }
  }
}
```

### Get Shipping Breakdown
```
GET /api/shipping/breakdown
```

Returns weight and dimension calculations for current cart.

### Test API Connection
```
GET /api/shipping/test
```

Tests connection to Endicia API.

## Usage in Checkout

### Basic Implementation

Add to your checkout blade template:

```blade
<div x-data="checkoutWithShipping()">
    <!-- Address Form -->
    <input type="text"
           x-model="shippingZip"
           placeholder="ZIP Code"
           @change="fetchShippingRates()">

    <!-- Loading State -->
    <div x-show="loadingRates">
        <p>Calculating shipping rates...</p>
    </div>

    <!-- Shipping Options -->
    <div x-show="ratesLoaded && !loadingRates">
        <template x-for="rate in shippingRates" :key="rate.service_code">
            <label class="shipping-option">
                <input type="radio"
                       name="shipping_method"
                       :value="rate.service_code"
                       x-model="selectedShippingMethod">
                <span x-text="rate.service_name"></span>
                <span x-text="'$' + rate.cost.toFixed(2)"></span>
                <small x-text="rate.delivery_days"></small>
            </label>
        </template>
    </div>

    <!-- Package Details -->
    <div x-show="shippingBreakdown && showShippingDetails">
        <h4>Package Details</h4>
        <p>Weight: <span x-text="formatWeight(shippingBreakdown.total_weight.weight_oz)"></span></p>
        <p>Package Type: <span x-text="shippingBreakdown.package_type"></span></p>
    </div>
</div>
```

Include the JavaScript:
```blade
<script src="{{ asset('js/checkout-shipping.js') }}"></script>
```

## Calculation Examples

### Example 1: Small Order (Envelope)
- 20 pages of Standard paper (0.25/page)
- Weight: 20 × 0.20oz = 4oz + 1.5oz packaging = 5.5oz
- Dimensions: 12" × 10" × 1" (envelope)
- Cost: ~$5-8 for Priority Mail

### Example 2: Large Order (Box)
- 100 pages of Premium paper (0.30/page)
- Weight: 100 × 0.24oz = 24oz + 4oz box = 28oz (1.75 lbs)
- Dimensions: 12" × 9" × 3" (box)
- Cost: ~$12-15 for Priority Mail

## Troubleshooting

### Rates Not Loading

1. **Check API credentials:**
   ```bash
   php artisan config:clear
   php artisan tinker
   >>> config('services.endicia.account_id')
   ```

2. **Test API connection:**
   ```bash
   curl -X GET http://localhost:8000/api/shipping/test
   ```

3. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Incorrect Weights

1. Weigh actual sample orders
2. Verify paper specs match reality
3. Update `PAPER_SPECS` constants
4. Account for packaging materials

### API Errors

Common errors and solutions:

- **Authentication Failed**: Check `ENDICIA_ACCOUNT_ID` and `ENDICIA_PASS_PHRASE`
- **Invalid ZIP Code**: Ensure ZIP is 5 digits
- **Service Unavailable**: Check if using test vs production URL
- **Weight Too Low**: Endicia has minimum weights per service

## Integration with Lunar PHP

When migrating to Lunar, shipping calculation can be integrated via:

1. **Lunar Shipping Modifiers** - Custom shipping calculation logic
2. **Lunar Order Events** - Calculate on order creation
3. **Custom Shipping Provider** - Implement Lunar's shipping interface

Example Lunar integration:
```php
// In AppServiceProvider
Lunar\Models\Cart::observe(CartObserver::class);

// In CartObserver
public function creating(Cart $cart)
{
    $calculator = new ShippingCalculator();
    $breakdown = $calculator->getShippingBreakdown($cart->lines->toArray());

    $cart->meta->put('shipping_breakdown', $breakdown);
}
```

## Testing

### Unit Tests

```php
// tests/Unit/ShippingCalculatorTest.php
public function test_calculates_weight_correctly()
{
    $cart = [
        'item1' => [
            'order_groups' => [
                ['paperType' => '0.25', 'totalPages' => 10]
            ],
            'quantity' => 1
        ]
    ];

    $calculator = new ShippingCalculator();
    $weight = $calculator->calculateWeight($cart);

    $this->assertEquals(3.5, $weight['weight_oz']); // 2oz pages + 1.5oz packaging
}
```

### Integration Tests

```bash
# Test with real API (requires credentials)
php artisan tinker

>>> $service = new App\Services\EndiciaService();
>>> $service->testConnection();
```

## Performance Considerations

1. **Caching**: Consider caching rates for same ZIP/weight combinations
2. **Async Loading**: Fetch rates via AJAX to avoid blocking checkout
3. **Fallback Rates**: Always provide static rates if API fails
4. **Rate Limiting**: Implement rate limiting to avoid API quota issues

## Security

1. **Never expose API credentials** in frontend code
2. **Validate input** before sending to Endicia
3. **Sanitize ZIP codes** to prevent injection
4. **Use HTTPS** for all API calls
5. **Store credentials** in environment variables only

## Production Checklist

Before going live:

- [ ] Switch to production Endicia API URL
- [ ] Update credentials to production account
- [ ] Test with real shipping addresses
- [ ] Verify weight calculations with actual samples
- [ ] Set up error monitoring (Sentry, Bugsnag)
- [ ] Configure rate caching
- [ ] Test fallback scenarios
- [ ] Update `ENDICIA_FROM_ZIP` to actual warehouse ZIP
- [ ] Set `ENDICIA_TEST_MODE=false`
- [ ] Document any paper spec customizations

## Support Resources

- **Endicia API Docs**: https://www.endicia.com/developer/docs/els.html
- **USPS Service Standards**: https://www.usps.com/ship/service-standards.htm
- **Paper Weight Guide**: https://www.paper-paper.com/weight.html
- **Laravel HTTP Client**: https://laravel.com/docs/http-client

## Future Enhancements

Potential improvements:

1. **International Shipping**: Add support for international addresses
2. **Insurance**: Calculate and offer shipping insurance
3. **Delivery Confirmation**: Add tracking/signature options
4. **Label Generation**: Generate shipping labels via Endicia
5. **Multi-warehouse**: Support shipping from multiple locations
6. **Rate Shopping**: Compare Endicia with other carriers (FedEx, UPS)
7. **Address Validation**: Use Endicia address verification
8. **Smart Boxing**: Algorithm to optimize box selection

## Maintenance

Regular tasks:

- **Monthly**: Verify API credentials still valid
- **Quarterly**: Review and update paper specifications
- **Annually**: Review Endicia pricing and adjust markup
- **As needed**: Update API endpoint if Endicia changes URLs

---

**Version**: 1.0
**Last Updated**: October 16, 2025
**Maintained By**: Development Team

# Endicia Shipping Integration - Data Flow

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          STAMP ALBUM DESIGNS                             │
│                      Endicia Shipping Integration                        │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 1: Customer Builds Order                                           │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Order Builder (/order)                                                  │
│  ┌────────────────────┐                                                 │
│  │ 1. Select Paper    │ → Economy ($0.20), Standard ($0.25), etc.      │
│  │ 2. Choose Country  │ → United States, Canada, etc.                   │
│  │ 3. Select Years    │ → 1990-2000                                     │
│  │ 4. Pick Pages      │ → Specific album pages                          │
│  └────────────────────┘                                                 │
│           ↓                                                              │
│  Cart Item Created:                                                      │
│  {                                                                       │
│    order_groups: [{                                                      │
│      paperType: "0.25",                                                  │
│      totalPages: 50,                                                     │
│      country: "USA"                                                      │
│    }],                                                                   │
│    quantity: 1,                                                          │
│    total: 12.50                                                          │
│  }                                                                       │
└─────────────────────────────────────────────────────────────────────────┘
                                  ↓
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 2: Checkout - Enter Shipping Address                               │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Checkout Page (/checkout)                                              │
│  ┌────────────────────┐                                                 │
│  │ Name: John Doe     │                                                 │
│  │ Address: 123 Main  │                                                 │
│  │ City: New York     │                                                 │
│  │ State: NY          │                                                 │
│  │ ZIP: 10001 ────────┼──→ Triggers rate calculation                   │
│  └────────────────────┘                                                 │
└─────────────────────────────────────────────────────────────────────────┘
                                  ↓
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 3: Weight & Dimension Calculation                                  │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ShippingCalculator Service                                             │
│  ┌────────────────────────────────────────────────────────────────┐    │
│  │ Input: Cart Items                                               │    │
│  │                                                                 │    │
│  │ Calculation:                                                    │    │
│  │   Paper Weight:  50 pages × 0.20 oz/page = 10.0 oz            │    │
│  │   Packaging:     Envelope (< 0.75" thick) = +1.5 oz            │    │
│  │   Total Weight:  11.5 oz (0.72 lbs)                            │    │
│  │                                                                 │    │
│  │   Dimensions:    12" × 10" × 1"                                │    │
│  │   Package Type:  Envelope (FlatRateEnvelope)                   │    │
│  │                                                                 │    │
│  │ Output:                                                         │    │
│  │   {                                                             │    │
│  │     total_weight: { weight_oz: 11.5, weight_lbs: 0.72 },      │    │
│  │     dimensions: { length: 12, width: 10, height: 1 },         │    │
│  │     package_type: "envelope"                                   │    │
│  │   }                                                             │    │
│  └────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
                                  ↓
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 4: Endicia API Request                                             │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  EndiciaService                                                          │
│  ┌────────────────────────────────────────────────────────────────┐    │
│  │ Build SOAP XML Request:                                         │    │
│  │                                                                 │    │
│  │ <CalculatePostageRateRequest>                                  │    │
│  │   <AccountID>your_account</AccountID>                          │    │
│  │   <PassPhrase>your_pass</PassPhrase>                           │    │
│  │   <MailClass>Priority</MailClass>                              │    │
│  │   <WeightOz>11.5</WeightOz>                                    │    │
│  │   <MailpieceShape>FlatRateEnvelope</MailpieceShape>           │    │
│  │   <FromPostalCode>90210</FromPostalCode>                       │    │
│  │   <ToPostalCode>10001</ToPostalCode>                           │    │
│  │ </CalculatePostageRateRequest>                                 │    │
│  │                                                                 │    │
│  │ Send to: https://elstestserver.endicia.com/...                │    │
│  └────────────────────────────────────────────────────────────────┘    │
│                                  ↓                                       │
│  ┌────────────────────────────────────────────────────────────────┐    │
│  │ Repeat for each mail class:                                    │    │
│  │   • Priority Mail                                              │    │
│  │   • Priority Mail Express                                      │    │
│  │   • First-Class Mail                                           │    │
│  │   • Media Mail                                                 │    │
│  └────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
                                  ↓
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 5: Endicia API Response                                            │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Parse XML Responses:                                                    │
│  ┌────────────────────────────────────────────────────────────────┐    │
│  │ Priority Mail:           $9.45 (2-3 business days)             │    │
│  │ Priority Mail Express:   $29.50 (1-2 business days)            │    │
│  │ First-Class Mail:        $5.99 (2-5 business days)             │    │
│  │ Media Mail:             $4.50 (2-8 business days)             │    │
│  └────────────────────────────────────────────────────────────────┘    │
│                                  ↓                                       │
│  Sort by Price:                                                          │
│  ┌────────────────────────────────────────────────────────────────┐    │
│  │ [                                                               │    │
│  │   { service: "Media Mail",         cost: 4.50 },              │    │
│  │   { service: "First-Class Mail",   cost: 5.99 },              │    │
│  │   { service: "Priority Mail",      cost: 9.45 },              │    │
│  │   { service: "Express",            cost: 29.50 }              │    │
│  │ ]                                                               │    │
│  └────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
                                  ↓
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 6: Display Options to Customer                                     │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Checkout Page Updates (via Alpine.js)                                  │
│  ┌────────────────────────────────────────────────────────────────┐    │
│  │                                                                 │    │
│  │  ◉ USPS Media Mail         $4.50   (2-8 business days)        │    │
│  │  ○ USPS First-Class Mail   $5.99   (2-5 business days)        │    │
│  │  ○ USPS Priority Mail      $9.45   (2-3 business days)        │    │
│  │  ○ USPS Priority Express   $29.50  (1-2 business days)        │    │
│  │                                                                 │    │
│  │  [i] Package Details                                           │    │
│  │      Weight: 11.5 oz (0.72 lbs)                               │    │
│  │      Package: Envelope (12" × 10" × 1")                       │    │
│  │                                                                 │    │
│  └────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
                                  ↓
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 7: Customer Completes Order                                        │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Order Summary                                                           │
│  ┌────────────────────────────────────────────────────────────────┐    │
│  │ Subtotal (50 pages):              $12.50                       │    │
│  │ Shipping (Media Mail):            $ 4.50                       │    │
│  │ ─────────────────────────────────────────                      │    │
│  │ Total:                            $17.00                       │    │
│  │                                                                 │    │
│  │ Order saved with:                                              │    │
│  │   • Selected shipping method: "media_mail"                     │    │
│  │   • Shipping cost: 4.50                                        │    │
│  │   • Package details in meta                                    │    │
│  └────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════════════
                            KEY COMPONENTS
═══════════════════════════════════════════════════════════════════════════

┌──────────────────────┐
│ ShippingCalculator   │  Converts cart → weight & dimensions
├──────────────────────┤
│ • calculateWeight()  │  Pages × paper weight + packaging
│ • calculateDimensions│  Envelope vs Box logic
│ • getBreakdown()     │  Detailed analysis
└──────────────────────┘

┌──────────────────────┐
│ EndiciaService       │  Communicates with Endicia API
├──────────────────────┤
│ • getRates()         │  Fetch all available rates
│ • buildXML()         │  Create SOAP request
│ • parseResponse()    │  Extract rates from XML
│ • testConnection()   │  Verify API access
└──────────────────────┘

┌──────────────────────┐
│ ShippingRateController│ API endpoints for frontend
├──────────────────────┤
│ POST /api/shipping/  │  Get rates for address
│      rates           │
│ GET  /api/shipping/  │  Get package breakdown
│      breakdown       │
│ GET  /api/shipping/  │  Test API connection
│      test            │
└──────────────────────┘


═══════════════════════════════════════════════════════════════════════════
                          PAPER SPECIFICATIONS
═══════════════════════════════════════════════════════════════════════════

  Paper Type    Price/Page   Weight/Page   Thickness   Description
  ────────────────────────────────────────────────────────────────────────
  Economy       $0.20        0.16 oz       0.004"      20lb bond
  Standard      $0.25        0.20 oz       0.005"      24lb bond  ← Default
  Premium       $0.30        0.24 oz       0.006"      28lb bond
  Deluxe        $0.35        0.28 oz       0.007"      32lb bond


═══════════════════════════════════════════════════════════════════════════
                         PACKAGING LOGIC
═══════════════════════════════════════════════════════════════════════════

  Condition               Package Type    Endicia Type        Weight Added
  ────────────────────────────────────────────────────────────────────────
  < 0.75" thickness       Envelope        FlatRateEnvelope    +1.5 oz
  ≥ 0.75" thickness       Box             Package             +4.0 oz

  Envelope: 12" × 10" × actual thickness
  Box:      12" × 9" × 3" (minimum)


═══════════════════════════════════════════════════════════════════════════
                        ERROR HANDLING
═══════════════════════════════════════════════════════════════════════════

  Scenario                        Response
  ────────────────────────────────────────────────────────────────────────
  Endicia API unavailable         → Use fallback static rates
  Invalid credentials             → Log error, show fallback
  No rates for destination        → Show fallback or error message
  Network timeout                 → Retry once, then fallback
  Invalid ZIP code                → Show error, ask for correction


═══════════════════════════════════════════════════════════════════════════
                         TESTING WORKFLOW
═══════════════════════════════════════════════════════════════════════════

  1. Configure .env with Endicia credentials
     ↓
  2. Run: php artisan config:clear
     ↓
  3. Run: php artisan endicia:test
     ↓
  4. Verify:
     ✓ Configuration loaded
     ✓ Weight calculated correctly
     ✓ API connection successful
     ✓ Rates returned
     ↓
  5. Test in browser:
     • Add items to cart
     • Go to checkout
     • Enter ZIP code
     • Watch rates load dynamically
     ↓
  6. Verify order saved with correct shipping cost


═══════════════════════════════════════════════════════════════════════════
                    EXAMPLE CALCULATION
═══════════════════════════════════════════════════════════════════════════

  Order:    100 pages of Premium paper ($0.30/page)
  
  Step 1: Calculate paper weight
          100 pages × 0.24 oz/page = 24 oz
  
  Step 2: Check thickness
          100 pages × 0.006" = 0.6" thick → Use ENVELOPE
  
  Step 3: Add packaging
          24 oz + 1.5 oz (envelope) = 25.5 oz (1.59 lbs)
  
  Step 4: Set dimensions
          12" × 10" × 1" (envelope)
  
  Step 5: Send to Endicia
          From: 90210 → To: 10001
          Weight: 25.5 oz
          Shape: FlatRateEnvelope
  
  Step 6: Receive rates
          • Priority Mail: $12.45
          • Express: $35.50
          • First-Class: $8.99
  
  Step 7: Customer selects & completes order
          Subtotal: $30.00 (100 pages × $0.30)
          Shipping: $ 8.99 (First-Class selected)
          ─────────────────
          Total:    $38.99
```

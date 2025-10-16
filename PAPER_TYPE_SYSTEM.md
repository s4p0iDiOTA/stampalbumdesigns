# Paper Type Management System

## Overview

A centralized system for managing paper types across the Stamp Album Designs application. All paper specifications, pricing, and attributes are defined in one location and used consistently throughout the site.

## Architecture

### Core Components

1. **Configuration** (`config/paper.php`)
   - Central repository for all paper type definitions
   - Includes specifications, pricing, features, and marketing information
   - Easy to update and maintain

2. **Model** (`app/Models/PaperType.php`)
   - Object-oriented interface for accessing paper types
   - Helper methods for calculations and queries
   - Type-safe access to attributes

3. **Controller** (`app/Http/Controllers/PaperTypeController.php`)
   - API endpoints for frontend integration
   - JSON responses for AJAX requests

4. **Service Integration**
   - ShippingCalculator uses PaperType for weight/dimension calculations
   - Lunar order generation uses paper types for metadata
   - Backward compatible with old price-based system

## Paper Type Attributes

Each paper type includes comprehensive specifications:

### Display Information
- **ID**: Unique identifier (`economy`, `standard`, `premium`, etc.)
- **Name**: Display name ("Economy Paper", "Standard Paper")
- **Description**: Marketing description
- **Badge**: Optional badge ("Best Value", "Most Popular")
- **Recommended For**: Use case description

### Pricing
- **Price Per Page**: Cost per page in dollars (0.20, 0.25, 0.30, 0.35)

### Physical Specifications
- **Weight Per Page**: Ounces per page (affects shipping)
- **Thickness**: Inches per page (affects packaging)
- **Paper Weight**: Bond weight in lbs (20lb, 24lb, 28lb, 32lb)
- **Width & Height**: Page dimensions in inches

### Features
- **Punches**: Hole punch configuration (2-hole, 3-hole, none)
- **Color**: Paper color (white, bright-white, cream, ivory)
- **Finish**: Surface finish (matte, smooth, premium-smooth, glossy)
- **Opacity**: Paper opacity percentage

### Management
- **Display Order**: Sort order in dropdowns
- **Is Active**: Whether selectable by customers
- **Is Default**: Default selection
- **SKU Prefix**: For generating SKUs

## Usage

### In PHP Code

```php
use App\Models\PaperType;

// Get all active paper types
$paperTypes = PaperType::all();

// Get specific paper type
$standard = PaperType::find('standard');

// Get default paper type
$default = PaperType::default();

// Access attributes
$name = $standard->getName();
$price = $standard->getPricePerPage();
$weight = $standard->getWeightPerPageOz();

// Calculations
$totalPrice = $standard->calculatePrice(50); // 50 pages
$totalWeight = $standard->calculateWeight(50); // in ounces
$thickness = $standard->calculateThickness(50); // in inches

// Generate SKU
$sku = $standard->generateSku('United States', '2024');
// Result: "STD-UNI-2024"

// Get specifications
$specs = $standard->getSpecifications();
```

### API Endpoints

#### Get All Paper Types
```
GET /api/paper-types

Response:
{
  "success": true,
  "paper_types": [
    {
      "id": "economy",
      "name": "Economy Paper",
      "description": "Budget-friendly option...",
      "price_per_page": 0.20,
      "dimensions": "8.5\" × 11\"",
      "punches": "3-hole",
      "color": "white",
      "finish": "matte",
      "badge": "Best Value",
      "recommended_for": "Casual collectors...",
      "is_default": false
    },
    ...
  ],
  "default": "standard"
}
```

#### Get Specific Paper Type
```
GET /api/paper-types/premium

Response:
{
  "success": true,
  "paper_type": {
    "id": "premium",
    "name": "Premium Paper",
    ...
  },
  "specifications": {
    "Physical": {
      "Dimensions": "8.5\" × 11\"",
      "Paper Weight": "28 lb bond",
      ...
    },
    ...
  }
}
```

#### Calculate Price/Weight
```
POST /api/paper-types/calculate
{
  "paper_type_id": "premium",
  "pages": 50
}

Response:
{
  "success": true,
  "paper_type": "Premium Paper",
  "pages": 50,
  "price_per_page": 0.30,
  "total_price": 15.00,
  "weight_oz": 12.0,
  "thickness_inches": 0.300
}
```

#### Get Compatible Paper Types for Album
```
GET /api/paper-types/album/scott-national

Response:
{
  "success": true,
  "album_type": "scott-national",
  "paper_types": [...]
}
```

### In JavaScript/Frontend

```javascript
// Fetch all paper types
fetch('/api/paper-types')
  .then(response => response.json())
  .then(data => {
    const paperTypes = data.paper_types;
    const defaultId = data.default;

    // Populate dropdown
    paperTypes.forEach(type => {
      const option = new Option(
        `${type.name} - $${type.price_per_page}/page`,
        type.id
      );
      if (type.is_default) option.selected = true;
      dropdown.appendChild(option);
    });
  });

// Calculate price dynamically
async function calculatePrice(paperTypeId, pages) {
  const response = await fetch('/api/paper-types/calculate', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
      paper_type_id: paperTypeId,
      pages: pages
    })
  });

  const data = await response.json();
  return data.total_price;
}
```

### In Blade Templates

```blade
@php
  $paperTypes = \App\Models\PaperType::all();
  $default = \App\Models\PaperType::default();
@endphp

<select name="paper_type_id">
  @foreach($paperTypes as $type)
    <option value="{{ $type->id }}"
            @if($type->isDefault()) selected @endif>
      {{ $type->name }} - ${{ number_format($type->price_per_page, 2) }}/page
      @if($type->badge)
        ({{ $type->badge }})
      @endif
    </option>
  @endforeach
</select>

<div class="paper-info">
  <h4>{{ $type->name }}</h4>
  <p>{{ $type->description }}</p>
  <ul>
    <li>Dimensions: {{ $type->dimensions_string }}</li>
    <li>{{ $type->punches }} punch</li>
    <li>{{ ucfirst($type->color) }} {{ $type->finish }} finish</li>
  </ul>
</div>
```

## Configuration Management

### Adding a New Paper Type

Edit `config/paper.php`:

```php
'types' => [
    // ... existing types ...

    'archive-quality' => [
        'id' => 'archive-quality',
        'name' => 'Archive Quality Paper',
        'description' => 'Museum-grade archival paper',
        'sku_prefix' => 'ARC',

        // Pricing
        'price_per_page' => 0.50,

        // Physical specifications
        'weight_per_page_oz' => 0.32,
        'thickness_inches' => 0.008,
        'paper_weight_lbs' => 40,

        // Dimensions
        'width' => 8.5,
        'height' => 11.0,

        // Features
        'punches' => '3-hole',
        'color' => 'bright-white',
        'finish' => 'premium-smooth',
        'opacity' => 99,

        // Display
        'display_order' => 5,
        'is_active' => true,
        'is_default' => false,

        // Marketing
        'badge' => 'Museum Grade',
        'recommended_for' => 'Rare stamps, long-term preservation',
    ],
],
```

### Modifying Existing Paper Type

Simply update the values in `config/paper.php`:

```php
'standard' => [
    // ... existing attributes ...
    'price_per_page' => 0.27,  // Updated price
    'badge' => 'Customer Favorite',  // Updated badge
],
```

### Deactivating a Paper Type

```php
'economy' => [
    // ... existing attributes ...
    'is_active' => false,  // Won't appear in customer-facing lists
],
```

## Backward Compatibility

The system maintains backward compatibility with the old price-based system:

```php
// Old format (still works)
$cart = [
    'item1' => [
        'order_groups' => [
            ['paperType' => '0.25', 'totalPages' => 50]  // Old: price as string
        ]
    ]
];

// New format (preferred)
$cart = [
    'item1' => [
        'order_groups' => [
            ['paperType' => 'standard', 'totalPages' => 50]  // New: ID as string
        ]
    ]
];

// Both work thanks to normalizePaperTypeId() in ShippingCalculator
```

The `ShippingCalculator::normalizePaperTypeId()` method automatically converts:
- `0.20` → `'economy'`
- `0.25` → `'standard'`
- `0.30` → `'premium'`
- `0.35` → `'deluxe'`

## Database Integration (Future)

For Lunar PHP integration, you can create a database table:

```bash
php artisan make:migration create_paper_types_table
```

```php
Schema::create('paper_types', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();  // 'economy', 'standard', etc.
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('sku_prefix', 10);
    $table->decimal('price_per_page', 8, 2);
    $table->decimal('weight_per_page_oz', 8, 4);
    $table->decimal('thickness_inches', 8, 4);
    $table->integer('paper_weight_lbs');
    $table->decimal('width', 8, 2);
    $table->decimal('height', 8, 2);
    $table->string('punches', 20);
    $table->string('color', 50);
    $table->string('finish', 50);
    $table->integer('opacity');
    $table->integer('display_order')->default(999);
    $table->boolean('is_active')->default(true);
    $table->boolean('is_default')->default(false);
    $table->string('badge')->nullable();
    $table->text('recommended_for')->nullable();
    $table->timestamps();
});
```

Then modify `PaperType` model to use Eloquent instead of config.

## Testing

### Unit Tests

```php
// tests/Unit/PaperTypeTest.php

public function test_can_get_all_paper_types()
{
    $types = PaperType::all();
    $this->assertGreaterThan(0, $types->count());
}

public function test_can_find_paper_type_by_id()
{
    $standard = PaperType::find('standard');
    $this->assertNotNull($standard);
    $this->assertEquals('Standard Paper', $standard->getName());
}

public function test_can_calculate_price()
{
    $standard = PaperType::find('standard');
    $price = $standard->calculatePrice(50);
    $this->assertEquals(12.50, $price); // 50 * 0.25
}

public function test_backward_compatibility_with_prices()
{
    $type = PaperType::findByPrice(0.25);
    $this->assertEquals('standard', $type->getId());
}
```

### Integration Tests

```php
public function test_paper_types_api_returns_all_types()
{
    $response = $this->get('/api/paper-types');
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'paper_types',
                 'default'
             ]);
}

public function test_can_calculate_via_api()
{
    $response = $this->postJson('/api/paper-types/calculate', [
        'paper_type_id' => 'standard',
        'pages' => 50
    ]);

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'total_price' => 12.50
             ]);
}
```

## Benefits of Centralization

1. **Single Source of Truth** - All paper specifications in one place
2. **Easy Updates** - Change price or specs in one location
3. **Type Safety** - Object-oriented interface prevents errors
4. **Consistency** - Same attributes used across entire application
5. **Extensibility** - Easy to add new attributes or paper types
6. **API Ready** - JSON endpoints for frontend consumption
7. **Documentation** - Self-documenting through method names
8. **Backward Compatible** - Works with existing cart data
9. **Future Proof** - Can easily migrate to database storage

## Migration Checklist

To fully adopt the centralized system:

- [x] Create `config/paper.php`
- [x] Create `app/Models/PaperType.php`
- [x] Create `app/Http/Controllers/PaperTypeController.php`
- [x] Update `ShippingCalculator` to use PaperType
- [x] Update `GenerateTestOrders` to use PaperType
- [x] Add API routes
- [ ] Update `order.blade.php` to fetch types from API
- [ ] Update `CheckoutController` to use paper type IDs
- [ ] Update cart display views
- [ ] Migrate existing session cart data (if needed)
- [ ] Add paper type selection UI improvements
- [ ] Create admin interface for managing paper types
- [ ] Add database table for paper types (optional)
- [ ] Update documentation and user guides

---

**Version**: 1.0
**Created**: October 16, 2025
**Status**: Implemented and Ready for Frontend Integration

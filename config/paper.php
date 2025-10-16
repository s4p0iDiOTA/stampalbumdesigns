<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paper Type Definitions
    |--------------------------------------------------------------------------
    |
    | Central repository for all paper types used in stamp album orders.
    | Each paper type includes physical specifications, pricing, and display
    | information used throughout the application.
    |
    */

    'types' => [
        'economy' => [
            'id' => 'economy',
            'name' => 'Economy Paper',
            'description' => 'Budget-friendly option, ideal for general collections',
            'sku_prefix' => 'ECO',
            
            // Pricing
            'price_per_page' => 0.20,
            
            // Physical specifications
            'weight_per_page_oz' => 0.16,      // Weight in ounces
            'thickness_inches' => 0.004,        // Thickness in inches
            'paper_weight_lbs' => 20,           // Paper weight (20lb bond)
            
            // Dimensions (inches)
            'width' => 8.5,
            'height' => 11.0,
            
            // Features
            'punches' => '3-hole',              // Hole punch configuration
            'color' => 'white',                 // Paper color
            'finish' => 'matte',                // Surface finish
            'opacity' => 85,                    // Opacity percentage
            
            // Display
            'display_order' => 1,
            'is_active' => true,
            'is_default' => false,
            
            // Marketing
            'badge' => 'Best Value',
            'recommended_for' => 'Casual collectors, backup copies',
        ],

        'standard' => [
            'id' => 'standard',
            'name' => 'Standard Paper',
            'description' => 'Most popular choice, perfect for regular use',
            'sku_prefix' => 'STD',
            
            // Pricing
            'price_per_page' => 0.25,
            
            // Physical specifications
            'weight_per_page_oz' => 0.20,
            'thickness_inches' => 0.005,
            'paper_weight_lbs' => 24,
            
            // Dimensions
            'width' => 8.5,
            'height' => 11.0,
            
            // Features
            'punches' => '3-hole',
            'color' => 'white',
            'finish' => 'matte',
            'opacity' => 90,
            
            // Display
            'display_order' => 2,
            'is_active' => true,
            'is_default' => true,  // Default selection
            
            // Marketing
            'badge' => 'Most Popular',
            'recommended_for' => 'General collections, everyday use',
        ],

        'premium' => [
            'id' => 'premium',
            'name' => 'Premium Paper',
            'description' => 'High-quality paper for valuable collections',
            'sku_prefix' => 'PRM',
            
            // Pricing
            'price_per_page' => 0.30,
            
            // Physical specifications
            'weight_per_page_oz' => 0.24,
            'thickness_inches' => 0.006,
            'paper_weight_lbs' => 28,
            
            // Dimensions
            'width' => 8.5,
            'height' => 11.0,
            
            // Features
            'punches' => '3-hole',
            'color' => 'bright-white',
            'finish' => 'smooth',
            'opacity' => 95,
            
            // Display
            'display_order' => 3,
            'is_active' => true,
            'is_default' => false,
            
            // Marketing
            'badge' => 'Premium Quality',
            'recommended_for' => 'Valuable collections, archival use',
        ],

        'deluxe' => [
            'id' => 'deluxe',
            'name' => 'Deluxe Paper',
            'description' => 'Professional-grade paper for museum-quality presentations',
            'sku_prefix' => 'DLX',
            
            // Pricing
            'price_per_page' => 0.35,
            
            // Physical specifications
            'weight_per_page_oz' => 0.28,
            'thickness_inches' => 0.007,
            'paper_weight_lbs' => 32,
            
            // Dimensions
            'width' => 8.5,
            'height' => 11.0,
            
            // Features
            'punches' => '3-hole',
            'color' => 'bright-white',
            'finish' => 'premium-smooth',
            'opacity' => 98,
            
            // Display
            'display_order' => 4,
            'is_active' => true,
            'is_default' => false,
            
            // Marketing
            'badge' => 'Professional Grade',
            'recommended_for' => 'Exhibition quality, rare stamps, long-term preservation',
        ],

        'international' => [
            'id' => 'international',
            'name' => 'Scott International',
            'description' => 'Compatible with Scott International albums',
            'sku_prefix' => 'INT',
            
            // Pricing
            'price_per_page' => 0.30,
            
            // Physical specifications
            'weight_per_page_oz' => 0.20,
            'thickness_inches' => 0.005,
            'paper_weight_lbs' => 24,
            
            // Dimensions
            'width' => 8.5,
            'height' => 11.0,
            
            // Features
            'punches' => '2-hole',  // Different hole configuration
            'color' => 'white',
            'finish' => 'matte',
            'opacity' => 90,
            
            // Display
            'display_order' => 5,
            'is_active' => true,
            'is_default' => false,
            
            // Marketing
            'badge' => 'Scott Compatible',
            'recommended_for' => 'Scott International album users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Punch Configurations
    |--------------------------------------------------------------------------
    |
    | Available hole punch configurations and their specifications.
    |
    */
    'punch_types' => [
        '2-hole' => [
            'name' => '2-Hole Punch',
            'description' => 'Standard 2-hole configuration',
            'hole_count' => 2,
            'hole_diameter' => 0.25, // inches
            'spacing' => 2.75, // inches between hole centers
        ],
        '3-hole' => [
            'name' => '3-Hole Punch',
            'description' => 'Standard 3-hole configuration',
            'hole_count' => 3,
            'hole_diameter' => 0.25,
            'spacing' => 4.25, // inches between outer holes
        ],
        'none' => [
            'name' => 'No Holes',
            'description' => 'Unpunched pages',
            'hole_count' => 0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Paper Colors
    |--------------------------------------------------------------------------
    |
    | Available paper colors and their specifications.
    |
    */
    'colors' => [
        'white' => [
            'name' => 'White',
            'hex' => '#FFFFFF',
            'description' => 'Standard white paper',
        ],
        'bright-white' => [
            'name' => 'Bright White',
            'hex' => '#FAFAFA',
            'description' => 'Ultra-bright white for enhanced contrast',
        ],
        'cream' => [
            'name' => 'Cream',
            'hex' => '#FFFDD0',
            'description' => 'Soft cream color, easy on the eyes',
        ],
        'ivory' => [
            'name' => 'Ivory',
            'hex' => '#FFFFF0',
            'description' => 'Classic ivory shade',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Paper Finishes
    |--------------------------------------------------------------------------
    |
    | Available paper surface finishes.
    |
    */
    'finishes' => [
        'matte' => 'Matte - Non-reflective surface',
        'smooth' => 'Smooth - Slightly smoother than matte',
        'premium-smooth' => 'Premium Smooth - Highest quality smooth finish',
        'glossy' => 'Glossy - Reflective surface (not recommended for stamps)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | Default paper type and other default settings.
    |
    */
    'defaults' => [
        'paper_type' => 'standard',
        'punches' => '3-hole',
        'color' => 'white',
        'finish' => 'matte',
    ],

    /*
    |--------------------------------------------------------------------------
    | Compatibility Matrix
    |--------------------------------------------------------------------------
    |
    | Which paper types are compatible with which album types.
    |
    */
    'album_compatibility' => [
        'scott-national' => ['economy', 'standard', 'premium', 'deluxe'],
        'scott-international' => ['international', 'standard', 'premium'],
        'minkus' => ['economy', 'standard', 'premium'],
        'harris' => ['economy', 'standard', 'premium', 'deluxe'],
        'custom' => ['economy', 'standard', 'premium', 'deluxe'],
    ],
];

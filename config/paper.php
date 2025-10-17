<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paper Sizes
    |--------------------------------------------------------------------------
    |
    | Base paper sizes with physical dimensions and specifications.
    | Each size defines available customization options.
    |
    */

    'sizes' => [
        '8.5x11' => [
            'id' => '8.5x11',
            'name' => '8.5" × 11"',
            'description' => 'Acid-free, heavyweight paper, ideal for general collections',
            'sku_prefix' => '85X11',

            // Dimensions (inches)
            'width' => 8.5,
            'height' => 11.0,

            // Base specifications (for 67lb paper with standard options)
            'base_weight_oz' => 0.32352,
            'base_thickness_inches' => 0.009,
            'base_price' => 0.20,

            // Available options for this size
            'available_options' => [
                'paper_weights' => ['67lb cardstock'],
                'colors' => ['cream', 'white'],
                'punches' => ['none', '3-hole'],
                'corners' => ['square', 'rounded'],
                'protection' => ['none', 'hingeless'],
            ],

            // Defaults for this size
            'default_options' => [
                'paper_weight' => '67lb cardstock',
                'color' => 'cream',
                'punches' => '3-hole',
                'corners' => 'square',
                'protection' => 'none',
            ],

            // Display
            'display_order' => 1,
            'is_active' => true,
            'is_default' => true,

            // Marketing
            'badge' => 'Best Value',
            'recommended_for' => 'General collections. About 300 pages per 3-inch D-ring binder.',
        ],

        'minkus' => [
            'id' => 'minkus',
            'name' => '9.5" × 11.25" (Minkus)',
            'description' => 'Minkus Global compatible size',
            'sku_prefix' => 'MNK',

            'width' => 9.5,
            'height' => 11.25,

            'base_weight_oz' => 0.2822,
            'base_thickness_inches' => 0.0065,
            'base_price' => 0.30,

            'available_options' => [
                'paper_weights' => ['70lb', '80lb'],
                'colors' => ['white'],
                'punches' => ['2-hole'],
                'corners' => ['square', 'rounded'],
                'protection' => ['none', 'hingeless'],
            ],

            'default_options' => [
                'paper_weight' => '80lb',
                'color' => 'white',
                'punches' => '2-hole',
                'corners' => 'square',
                'protection' => 'none',
            ],

            'display_order' => 2,
            'is_active' => true,
            'is_default' => false,

            'badge' => 'Minkus Compatible',
            'recommended_for' => 'Used with Minkus Global albums and similar sizes.',
        ],

        'international' => [
            'id' => 'international',
            'name' => '9.25" × 11.25" International',
            'description' => 'Scott International compatible size',
            'sku_prefix' => 'INT',

            'width' => 9.25,
            'height' => 11.25,

            'base_weight_oz' => 0.27161,
            'base_thickness_inches' => 0.0065,
            'base_price' => 0.30,

            'available_options' => [
                'paper_weights' => ['80lb'],
                'colors' => ['cougar-natural'],
                'punches' => ['2-hole'],
                'corners' => ['square', 'rounded'],
                'protection' => ['none', 'hingeless'],
            ],

            'default_options' => [
                'paper_weight' => '80lb',
                'color' => 'cougar-natural',
                'punches' => '2-hole',
                'corners' => 'square',
                'protection' => 'none',
            ],

            'display_order' => 3,
            'is_active' => true,
            'is_default' => false,

            'badge' => 'Scott International Compatible',
            'recommended_for' => 'Used with Scott International albums and similar sizes.',
        ],

        'specialized' => [
            'id' => 'specialized',
            'name' => '10.5" × 12" Specialized',
            'description' => 'Scott Specialized compatible, premium quality',
            'sku_prefix' => 'SPC',

            'width' => 10.5,
            'height' => 12.0,

            'base_weight_oz' => 0.31747,
            'base_thickness_inches' => 0.0065,
            'base_price' => 0.35,

            'available_options' => [
                'paper_weights' => ['80lb'],
                'colors' => ['cougar-natural'],
                'punches' => ['none', '2-hole-rect', '3-hole'],
                'corners' => ['square', 'rounded'],
                'protection' => ['none', 'hingeless'],
            ],

            'default_options' => [
                'paper_weight' => '80lb',
                'color' => 'cougar-natural',
                'punches' => '2-hole-rect',
                'corners' => 'square',
                'protection' => 'none',
            ],

            'display_order' => 4,
            'is_active' => true,
            'is_default' => false,

            'badge' => 'Scott Specialized Compatible',
            'recommended_for' => 'Used with Scott Specialized albums and similar sizes.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Paper Options
    |--------------------------------------------------------------------------
    |
    | Customization options available for paper sizes.
    | Each option includes price modifiers and physical impacts.
    |
    */

    'options' => [

        // Paper Weight Options
        'paper_weights' => [
            '67lb cardstock' => [
                'id' => '67lb',
                'name' => '67lb Cardstock',
                'description' => 'Standard weight, good for general collections',
                'weight_multiplier' => 1.0,
                'thickness_multiplier' => 1.0,
                'price_modifier' => 0.00,
                'display_order' => 1,
            ],
            '70lb' => [
                'id' => '70lb',
                'name' => '70lb Paper',
                'description' => 'Heavier weight, more rigid',
                'weight_multiplier' => 1.194,  // 80/67
                'thickness_multiplier' => 1.15,
                'price_modifier' => 0.05,
                'display_order' => 2,
            ],
            '80lb' => [
                'id' => '80lb',
                'name' => '80lb Paper',
                'description' => 'Heavier weight, more rigid',
                'weight_multiplier' => 1.194,  // 80/67
                'thickness_multiplier' => 1.15,
                'price_modifier' => 0.05,
                'display_order' => 2,
            ]
        ],

        // Color Options
        'colors' => [
            'cream' => [
                'id' => 'cream',
                'name' => 'Cream',
                'description' => 'Soft cream color, easy on the eyes',
                'hex' => '#FFFDD0',
                'price_modifier' => 0.00,
                'display_order' => 1,
            ],
            'white' => [
                'id' => 'white',
                'name' => 'Bright White',
                'description' => 'Standard white paper',
                'hex' => '#FFFFFF',
                'price_modifier' => 0.00,
                'display_order' => 2,
            ],
            'cougar-natural' => [
                'id' => 'cougar-natural',
                'name' => 'Cougar Natural',
                'description' => 'Natural off-white shade, archive quality',
                'hex' => '#FAF8F3',
                'price_modifier' => 0.02,
                'display_order' => 3,
            ],
        ],

        // Punch Options
        'punches' => [
            'none' => [
                'id' => 'none',
                'name' => 'No Holes',
                'description' => 'Unpunched pages (you punch yourself)',
                'hole_count' => 0,
                'hole_shape' => null,
                'price_modifier' => -0.02,
                'display_order' => 1,
            ],
            '2-hole' => [
                'id' => '2-hole',
                'name' => '2-Hole Punch',
                'description' => 'Standard 2-hole round configuration',
                'hole_count' => 2,
                'hole_shape' => 'round',
                'hole_diameter' => 0.25,
                'spacing' => 2.75,
                'price_modifier' => 0.00,
                'display_order' => 2,
            ],
            '2-hole-rect' => [
                'id' => '2-hole-rect',
                'name' => '2-Hole Rectangular',
                'description' => 'Rectangular slots for reinforced binding',
                'hole_count' => 2,
                'hole_shape' => 'rectangular',
                'hole_width' => 0.375,
                'hole_height' => 0.125,
                'spacing' => 2.75,
                'price_modifier' => 0.03,
                'display_order' => 3,
            ],
            '3-hole' => [
                'id' => '3-hole',
                'name' => '3-Hole Punch',
                'description' => 'Standard 3-hole round configuration',
                'hole_count' => 3,
                'hole_shape' => 'round',
                'hole_diameter' => 0.25,
                'spacing' => 4.25,
                'price_modifier' => 0.00,
                'display_order' => 4,
            ],
        ],

        // Corner Options
        'corners' => [
            'square' => [
                'id' => 'square',
                'name' => 'Square Corners',
                'description' => 'Standard square corners',
                'price_modifier' => 0.00,
                'display_order' => 1,
            ],
            'rounded' => [
                'id' => 'rounded',
                'name' => 'Rounded Corners',
                'description' => 'Professional rounded corners (1/8" radius)',
                'radius' => 0.125,
                'price_modifier' => 0.03,
                'display_order' => 2,
            ],
        ],

        // Protection Options
        'protection' => [
            'none' => [
                'id' => 'none',
                'name' => 'Standard',
                'description' => 'Standard paper without mounts',
                'price_modifier' => 0.00,
                'display_order' => 1,
            ],
            'hingeless' => [
                'id' => 'hingeless',
                'name' => 'Hingeless Mounts',
                'description' => 'Pre-attached crystal clear hingeless mounts',
                'price_modifier' => 0.50,
                'weight_modifier_oz' => 0.05,
                'display_order' => 2,
            ],
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | System-wide default paper configuration.
    |
    */

    'defaults' => [
        'paper_size' => '8.5x11',
    ],
];

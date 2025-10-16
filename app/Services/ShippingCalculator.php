<?php

namespace App\Services;

use App\Models\PaperType;

/**
 * ShippingCalculator
 * 
 * Handles conversion of stamp album pages to shipping weight and dimensions.
 * Uses centralized PaperType model for paper specifications.
 */
class ShippingCalculator
{

    /**
     * Packaging specifications
     */
    private const PACKAGING = [
        'envelope_weight_oz' => 1.0,        // Weight of envelope/mailer
        'padding_weight_oz' => 0.5,         // Bubble wrap, cardboard backing
        'max_envelope_thickness' => 0.75,   // Max thickness for envelope (inches)
        'box_weight_oz' => 4.0,            // Weight of small box
        'box_dimensions' => [              // Standard small box (inches)
            'length' => 12,
            'width' => 9,
            'height' => 3,
        ],
    ];

    /**
     * Calculate total weight for the order
     * 
     * @param array $cart Cart items from session
     * @return array ['weight_oz' => float, 'weight_lbs' => float]
     */
    public function calculateWeight(array $cart): array
    {
        $totalWeightOz = 0;

        foreach ($cart as $item) {
            if (isset($item['order_groups']) && isset($item['quantity'])) {
                foreach ($item['order_groups'] as $group) {
                    $paperTypeId = $this->normalizePaperTypeId($group['paperType'] ?? null);
                    $totalPages = $group['totalPages'] ?? 0;
                    $quantity = $item['quantity'] ?? 1;

                    // Get paper type from centralized model
                    $paperType = PaperType::find($paperTypeId) ?? PaperType::default();
                    
                    // Calculate weight for this group
                    $totalWeightOz += $paperType->getWeightPerPageOz() * $totalPages * $quantity;
                }
            }
        }

        // Add packaging weight
        $packageType = $this->determinePackageType($cart);
        $totalWeightOz += $packageType['container_weight'];

        return [
            'weight_oz' => round($totalWeightOz, 2),
            'weight_lbs' => round($totalWeightOz / 16, 2),
        ];
    }

    /**
     * Calculate package dimensions
     * 
     * @param array $cart Cart items from session
     * @return array ['length' => int, 'width' => int, 'height' => int, 'type' => string]
     */
    public function calculateDimensions(array $cart): array
    {
        $totalThickness = 0;
        $maxLength = 0;
        $maxWidth = 0;

        foreach ($cart as $item) {
            if (isset($item['order_groups']) && isset($item['quantity'])) {
                foreach ($item['order_groups'] as $group) {
                    $paperTypeId = $this->normalizePaperTypeId($group['paperType'] ?? null);
                    $totalPages = $group['totalPages'] ?? 0;
                    $quantity = $item['quantity'] ?? 1;

                    // Get paper type from centralized model
                    $paperType = PaperType::find($paperTypeId) ?? PaperType::default();
                    
                    // Calculate thickness for this group
                    $totalThickness += $paperType->getThicknessInches() * $totalPages * $quantity;
                    
                    // Track maximum dimensions
                    $maxLength = max($maxLength, $paperType->getHeight());
                    $maxWidth = max($maxWidth, $paperType->getWidth());
                }
            }
        }

        // Add envelope padding
        $maxLength = ceil($maxLength) + 1;
        $maxWidth = ceil($maxWidth) + 1;

        // Determine if we need an envelope or box
        if ($totalThickness <= self::PACKAGING['max_envelope_thickness']) {
            return [
                'length' => $maxLength,
                'width' => $maxWidth,
                'height' => max(1, ceil($totalThickness)), // Minimum 1 inch for envelope
                'type' => 'envelope',
                'endicia_type' => 'FlatRateEnvelope', // or 'Flat'
            ];
        } else {
            return [
                'length' => self::PACKAGING['box_dimensions']['length'],
                'width' => self::PACKAGING['box_dimensions']['width'],
                'height' => max(
                    self::PACKAGING['box_dimensions']['height'],
                    ceil($totalThickness)
                ),
                'type' => 'box',
                'endicia_type' => 'Package',
            ];
        }
    }

    /**
     * Determine package type and its weight
     * 
     * @param array $cart
     * @return array ['type' => string, 'container_weight' => float]
     */
    private function determinePackageType(array $cart): array
    {
        $dimensions = $this->calculateDimensions($cart);
        
        if ($dimensions['type'] === 'envelope') {
            return [
                'type' => 'envelope',
                'container_weight' => self::PACKAGING['envelope_weight_oz'] + 
                                     self::PACKAGING['padding_weight_oz'],
            ];
        } else {
            return [
                'type' => 'box',
                'container_weight' => self::PACKAGING['box_weight_oz'],
            ];
        }
    }

    /**
     * Get detailed breakdown of cart for shipping
     * 
     * @param array $cart
     * @return array
     */
    public function getShippingBreakdown(array $cart): array
    {
        $weight = $this->calculateWeight($cart);
        $dimensions = $this->calculateDimensions($cart);
        $paperTypeBreakdown = [];

        // Calculate pages by paper type
        foreach ($cart as $item) {
            if (isset($item['order_groups']) && isset($item['quantity'])) {
                foreach ($item['order_groups'] as $group) {
                    $paperTypeId = $this->normalizePaperTypeId($group['paperType'] ?? null);
                    $totalPages = $group['totalPages'] ?? 0;
                    $quantity = $item['quantity'] ?? 1;

                    if (!isset($paperTypeBreakdown[$paperTypeId])) {
                        $paperType = PaperType::find($paperTypeId) ?? PaperType::default();
                        $paperTypeBreakdown[$paperTypeId] = [
                            'id' => $paperType->getId(),
                            'name' => $paperType->getName(),
                            'pages' => 0,
                            'weight_oz' => 0,
                            'price_per_page' => $paperType->getPricePerPage(),
                        ];
                    }

                    $paperType = PaperType::find($paperTypeId) ?? PaperType::default();
                    $paperTypeBreakdown[$paperTypeId]['pages'] += $totalPages * $quantity;
                    $paperTypeBreakdown[$paperTypeId]['weight_oz'] += 
                        $paperType->getWeightPerPageOz() * $totalPages * $quantity;
                }
            }
        }

        return [
            'total_weight' => $weight,
            'dimensions' => $dimensions,
            'paper_types' => $paperTypeBreakdown,
            'package_type' => $dimensions['type'],
        ];
    }

    /**
     * Normalize paper type identifier (backward compatibility)
     * Converts old price-based IDs to new paper type IDs
     * 
     * @param mixed $paperTypeValue
     * @return string
     */
    private function normalizePaperTypeId($paperTypeValue): string
    {
        if (is_null($paperTypeValue)) {
            return 'standard';
        }

        // If it's already a paper type ID (string), return it
        if (is_string($paperTypeValue) && !is_numeric($paperTypeValue)) {
            return $paperTypeValue;
        }

        // If it's a numeric price, convert to paper type ID
        $price = (float) $paperTypeValue;
        
        // Map old prices to new IDs (backward compatibility)
        $priceMap = [
            0.20 => 'economy',
            0.25 => 'standard',
            0.30 => 'premium',
            0.35 => 'deluxe',
        ];

        foreach ($priceMap as $mappedPrice => $id) {
            if (abs($price - $mappedPrice) < 0.001) {
                return $id;
            }
        }

        // Try finding by price if not in map
        $paperType = PaperType::findByPrice($price);
        if ($paperType) {
            return $paperType->getId();
        }

        // Default fallback
        return 'standard';
    }

    /**
     * Get paper specification by type (deprecated - use PaperType::find() instead)
     * 
     * @param string $paperType
     * @return array|null
     * @deprecated Use PaperType::find() instead
     */
    public static function getPaperSpec(string $paperType): ?array
    {
        $type = PaperType::find($paperType);
        return $type ? $type->toArray() : null;
    }

    /**
     * Get all paper specifications (deprecated - use PaperType::all() instead)
     * 
     * @return array
     * @deprecated Use PaperType::all() instead
     */
    public static function getAllPaperSpecs(): array
    {
        return PaperType::all()->mapWithKeys(fn($type) => [
            $type->getId() => $type->toArray()
        ])->toArray();
    }
}

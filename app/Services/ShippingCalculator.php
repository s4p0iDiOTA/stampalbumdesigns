<?php

namespace App\Services;

use App\Models\PaperConfiguration;
use App\Models\PaperSize;

/**
 * ShippingCalculator
 *
 * Handles conversion of stamp album pages to shipping weight and dimensions.
 * Uses PaperConfiguration model for paper specifications.
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
                    $totalPages = $group['totalPages'] ?? 0;
                    $quantity = $item['quantity'] ?? 1;

                    // Get paper configuration
                    $config = $this->getPaperConfiguration($group);

                    // Calculate weight for this group
                    $totalWeightOz += $config->calculateWeightPerPage() * $totalPages * $quantity;
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
                    $totalPages = $group['totalPages'] ?? 0;
                    $quantity = $item['quantity'] ?? 1;

                    // Get paper configuration
                    $config = $this->getPaperConfiguration($group);
                    $size = $config->getSize();

                    // Calculate thickness for this group
                    $totalThickness += $config->calculateThicknessPerPage() * $totalPages * $quantity;

                    // Track maximum dimensions
                    $maxLength = max($maxLength, $size->getHeight());
                    $maxWidth = max($maxWidth, $size->getWidth());
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
        $paperConfigBreakdown = [];

        // Calculate pages by paper configuration
        foreach ($cart as $item) {
            if (isset($item['order_groups']) && isset($item['quantity'])) {
                foreach ($item['order_groups'] as $group) {
                    $totalPages = $group['totalPages'] ?? 0;
                    $quantity = $item['quantity'] ?? 1;

                    $config = $this->getPaperConfiguration($group);
                    $configKey = $config->generateSku();

                    if (!isset($paperConfigBreakdown[$configKey])) {
                        $paperConfigBreakdown[$configKey] = [
                            'sku' => $configKey,
                            'name' => $config->getDisplayName(),
                            'pages' => 0,
                            'weight_oz' => 0,
                            'price_per_page' => $config->calculatePricePerPage(),
                        ];
                    }

                    $paperConfigBreakdown[$configKey]['pages'] += $totalPages * $quantity;
                    $paperConfigBreakdown[$configKey]['weight_oz'] +=
                        $config->calculateWeightPerPage() * $totalPages * $quantity;
                }
            }
        }

        return [
            'total_weight' => $weight,
            'dimensions' => $dimensions,
            'paper_configurations' => $paperConfigBreakdown,
            'package_type' => $dimensions['type'],
        ];
    }

    /**
     * Get paper configuration from order group data
     *
     * @param array $group Order group with paper configuration
     * @return PaperConfiguration
     */
    private function getPaperConfiguration(array $group): PaperConfiguration
    {
        // New format: size + options
        if (isset($group['paper_size']) && isset($group['paper_options'])) {
            return new PaperConfiguration(
                $group['paper_size'],
                $group['paper_options']
            );
        }

        // Fallback: use default configuration for default size
        $defaultSize = PaperSize::default();
        return new PaperConfiguration(
            $defaultSize->getId(),
            $defaultSize->getDefaultOptions()
        );
    }
}

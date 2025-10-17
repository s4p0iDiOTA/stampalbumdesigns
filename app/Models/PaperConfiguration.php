<?php

namespace App\Models;

use InvalidArgumentException;

/**
 * PaperConfiguration
 *
 * Represents a specific paper configuration: a base size with selected options.
 * Handles price calculation, weight calculation, and SKU generation.
 */
class PaperConfiguration
{
    protected PaperSize $size;
    protected array $options;

    /**
     * Create a new paper configuration
     *
     * @param string $sizeId
     * @param array $options ['paper_weight' => '67lb', 'color' => 'cream', ...]
     * @throws InvalidArgumentException
     */
    public function __construct(string $sizeId, array $options = [])
    {
        $this->size = PaperSize::find($sizeId);

        if (!$this->size) {
            throw new InvalidArgumentException("Paper size '{$sizeId}' not found");
        }

        // Use provided options or defaults
        $this->options = array_merge($this->size->getDefaultOptions(), $options);
    }

    /**
     * Create from array data
     *
     * @param array $data ['size' => '8.5x11', 'options' => [...]]
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['size'] ?? $data['paper_size'] ?? '',
            $data['options'] ?? $data['paper_options'] ?? []
        );
    }

    /**
     * Calculate price per page
     *
     * @return float
     */
    public function calculatePricePerPage(): float
    {
        $pricePerPage = $this->size->getBasePrice();

        // Add price modifiers from each option
        foreach ($this->options as $optionType => $optionId) {
            $option = config("paper.options.{$optionType}.{$optionId}");
            if ($option && isset($option['price_modifier'])) {
                $pricePerPage += $option['price_modifier'];
            }
        }

        return round($pricePerPage, 2);
    }

    /**
     * Calculate total price for given number of pages
     *
     * @param int $pages
     * @return float
     */
    public function calculatePrice(int $pages): float
    {
        return round($this->calculatePricePerPage() * $pages, 2);
    }

    /**
     * Calculate weight per page in ounces
     *
     * @return float
     */
    public function calculateWeightPerPage(): float
    {
        $weightPerPage = $this->size->getBaseWeightOz();

        // Apply weight multiplier from paper weight option
        if (isset($this->options['paper_weight'])) {
            $weightOption = config("paper.options.paper_weights.{$this->options['paper_weight']}");
            if ($weightOption && isset($weightOption['weight_multiplier'])) {
                $weightPerPage *= $weightOption['weight_multiplier'];
            }
        }

        // Add absolute weight modifiers (e.g., from protection options)
        foreach ($this->options as $optionType => $optionId) {
            $option = config("paper.options.{$optionType}.{$optionId}");
            if ($option && isset($option['weight_modifier_oz'])) {
                $weightPerPage += $option['weight_modifier_oz'];
            }
        }

        return round($weightPerPage, 5);
    }

    /**
     * Calculate total weight for given number of pages
     *
     * @param int $pages
     * @return array ['weight_oz' => float, 'weight_lbs' => float]
     */
    public function calculateWeight(int $pages): array
    {
        $weightOz = $this->calculateWeightPerPage() * $pages;

        return [
            'weight_oz' => round($weightOz, 2),
            'weight_lbs' => round($weightOz / 16, 2),
        ];
    }

    /**
     * Calculate thickness per page in inches
     *
     * @return float
     */
    public function calculateThicknessPerPage(): float
    {
        $thickness = $this->size->getBaseThickness();

        // Apply thickness multiplier from paper weight option
        if (isset($this->options['paper_weight'])) {
            $weightOption = config("paper.options.paper_weights.{$this->options['paper_weight']}");
            if ($weightOption && isset($weightOption['thickness_multiplier'])) {
                $thickness *= $weightOption['thickness_multiplier'];
            }
        }

        return round($thickness, 5);
    }

    /**
     * Calculate total thickness for given number of pages
     *
     * @param int $pages
     * @return float
     */
    public function calculateThickness(int $pages): float
    {
        return round($this->calculateThicknessPerPage() * $pages, 3);
    }

    /**
     * Generate SKU for this configuration
     *
     * @return string
     */
    public function generateSku(): string
    {
        $sku = $this->size->getSkuPrefix();

        // Add paper weight
        if (isset($this->options['paper_weight'])) {
            $sku .= '-' . strtoupper(str_replace('lb', 'LB', $this->options['paper_weight']));
        }

        // Add color (first 3 letters)
        if (isset($this->options['color'])) {
            $colorCode = strtoupper(substr($this->options['color'], 0, 3));
            $sku .= '-' . $colorCode;
        }

        // Add punches
        if (isset($this->options['punches'])) {
            $punchCode = strtoupper(str_replace('-hole', 'H', $this->options['punches']));
            $punchCode = str_replace('NONE', '0H', $punchCode);
            $sku .= '-' . $punchCode;
        }

        // Add corners
        if (isset($this->options['corners'])) {
            $cornerCode = strtoupper(substr($this->options['corners'], 0, 2));
            $sku .= '-' . $cornerCode;
        }

        // Add protection
        if (isset($this->options['protection']) && $this->options['protection'] !== 'none') {
            $protectionCode = strtoupper(substr($this->options['protection'], 0, 3));
            $sku .= '-' . $protectionCode;
        }

        return $sku;
    }

    /**
     * Get display name for this configuration
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        $name = $this->size->getName();

        $parts = [];

        // Add paper weight if not default
        if (isset($this->options['paper_weight'])) {
            $weightOption = config("paper.options.paper_weights.{$this->options['paper_weight']}");
            if ($weightOption) {
                $parts[] = $weightOption['name'];
            }
        }

        // Add color if specified
        if (isset($this->options['color'])) {
            $colorOption = config("paper.options.colors.{$this->options['color']}");
            if ($colorOption) {
                $parts[] = $colorOption['name'];
            }
        }

        // Add punches
        if (isset($this->options['punches'])) {
            $punchOption = config("paper.options.punches.{$this->options['punches']}");
            if ($punchOption) {
                $parts[] = $punchOption['name'];
            }
        }

        // Add special features
        if (isset($this->options['corners']) && $this->options['corners'] !== 'square') {
            $cornerOption = config("paper.options.corners.{$this->options['corners']}");
            if ($cornerOption) {
                $parts[] = $cornerOption['name'];
            }
        }

        if (isset($this->options['protection']) && $this->options['protection'] !== 'none') {
            $protectionOption = config("paper.options.protection.{$this->options['protection']}");
            if ($protectionOption) {
                $parts[] = $protectionOption['name'];
            }
        }

        if (!empty($parts)) {
            $name .= ' - ' . implode(', ', $parts);
        }

        return $name;
    }

    /**
     * Validate this configuration
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validate(): array
    {
        $errors = [];

        // Check each option is available for this size
        foreach ($this->options as $optionType => $optionId) {
            if (!$this->size->isOptionAvailable($optionType, $optionId)) {
                $errors[] = "Option '{$optionId}' is not available for {$optionType} on this paper size";
            }
        }

        // Add any business logic validations
        // Example: hingeless protection requires holes
        if (isset($this->options['protection']) && $this->options['protection'] === 'hingeless') {
            if (isset($this->options['punches']) && $this->options['punches'] === 'none') {
                $errors[] = "Hingeless mounts require hole punches";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get all specifications for this configuration
     *
     * @param int|null $pages Optional number of pages for calculations
     * @return array
     */
    public function getSpecifications(int $pages = null): array
    {
        $specs = [
            'size' => $this->size->toArray(),
            'options' => $this->options,
            'sku' => $this->generateSku(),
            'display_name' => $this->getDisplayName(),
            'price_per_page' => $this->calculatePricePerPage(),
            'weight_per_page_oz' => $this->calculateWeightPerPage(),
            'thickness_per_page_inches' => $this->calculateThicknessPerPage(),
        ];

        if ($pages !== null) {
            $specs['total_pages'] = $pages;
            $specs['total_price'] = $this->calculatePrice($pages);
            $specs['total_weight'] = $this->calculateWeight($pages);
            $specs['total_thickness_inches'] = $this->calculateThickness($pages);
        }

        return $specs;
    }

    /**
     * Get the paper size
     *
     * @return PaperSize
     */
    public function getSize(): PaperSize
    {
        return $this->size;
    }

    /**
     * Get the selected options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'size' => $this->size->getId(),
            'options' => $this->options,
            'sku' => $this->generateSku(),
            'display_name' => $this->getDisplayName(),
            'price_per_page' => $this->calculatePricePerPage(),
        ];
    }

    /**
     * Convert to JSON
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}

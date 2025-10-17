<?php

namespace App\Models;

use Illuminate\Support\Collection;

/**
 * PaperSize
 *
 * Represents a base paper size with physical dimensions and specifications.
 * Paper sizes can have multiple customization options applied to them.
 */
class PaperSize
{
    protected array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get all active paper sizes
     *
     * @return Collection<PaperSize>
     */
    public static function all(): Collection
    {
        $sizes = config('paper.sizes', []);

        return collect($sizes)
            ->filter(fn($size) => $size['is_active'] ?? true)
            ->sortBy('display_order')
            ->map(fn($size) => new self($size))
            ->values();
    }

    /**
     * Get all paper sizes including inactive
     *
     * @return Collection<PaperSize>
     */
    public static function allWithInactive(): Collection
    {
        $sizes = config('paper.sizes', []);

        return collect($sizes)
            ->sortBy('display_order')
            ->map(fn($size) => new self($size))
            ->values();
    }

    /**
     * Find a paper size by ID
     *
     * @param string $id
     * @return PaperSize|null
     */
    public static function find(string $id): ?self
    {
        $size = config("paper.sizes.{$id}");

        return $size ? new self($size) : null;
    }

    /**
     * Get the default paper size
     *
     * @return PaperSize
     */
    public static function default(): self
    {
        $defaultId = config('paper.defaults.paper_size', '8.5x11');
        return self::find($defaultId) ?? self::find('8.5x11');
    }

    /**
     * Get available options for this paper size
     *
     * @param string $optionType (paper_weights, colors, punches, corners, protection)
     * @return Collection
     */
    public function getAvailableOptions(string $optionType): Collection
    {
        $availableIds = $this->attributes['available_options'][$optionType] ?? [];
        $allOptions = config("paper.options.{$optionType}", []);

        return collect($availableIds)
            ->map(fn($id) => $allOptions[$id] ?? null)
            ->filter()
            ->sortBy('display_order')
            ->values();
    }

    /**
     * Get all available options for this paper size
     *
     * @return array
     */
    public function getAllAvailableOptions(): array
    {
        $optionTypes = ['paper_weights', 'colors', 'punches', 'corners', 'protection'];
        $result = [];

        foreach ($optionTypes as $type) {
            $result[$type] = $this->getAvailableOptions($type)->toArray();
        }

        return $result;
    }

    /**
     * Check if a specific option is available for this size
     *
     * @param string $optionType
     * @param string $optionId
     * @return bool
     */
    public function isOptionAvailable(string $optionType, string $optionId): bool
    {
        $availableIds = $this->attributes['available_options'][$optionType] ?? [];
        return in_array($optionId, $availableIds);
    }

    /**
     * Get default options for this size
     *
     * @return array
     */
    public function getDefaultOptions(): array
    {
        return $this->attributes['default_options'] ?? [];
    }

    // Getters for basic attributes

    public function getId(): string
    {
        return $this->attributes['id'];
    }

    public function getName(): string
    {
        return $this->attributes['name'];
    }

    public function getDescription(): string
    {
        return $this->attributes['description'] ?? '';
    }

    public function getSkuPrefix(): string
    {
        return $this->attributes['sku_prefix'] ?? strtoupper($this->getId());
    }

    public function getWidth(): float
    {
        return $this->attributes['width'];
    }

    public function getHeight(): float
    {
        return $this->attributes['height'];
    }

    public function getBaseWeightOz(): float
    {
        return $this->attributes['base_weight_oz'];
    }

    public function getBaseThickness(): float
    {
        return $this->attributes['base_thickness_inches'];
    }

    public function getBasePrice(): float
    {
        return $this->attributes['base_price'];
    }

    public function getDisplayOrder(): int
    {
        return $this->attributes['display_order'] ?? 999;
    }

    public function isActive(): bool
    {
        return $this->attributes['is_active'] ?? true;
    }

    public function isDefault(): bool
    {
        return $this->attributes['is_default'] ?? false;
    }

    public function getBadge(): ?string
    {
        return $this->attributes['badge'] ?? null;
    }

    public function getRecommendedFor(): ?string
    {
        return $this->attributes['recommended_for'] ?? null;
    }

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'sku_prefix' => $this->getSkuPrefix(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'base_weight_oz' => $this->getBaseWeightOz(),
            'base_thickness_inches' => $this->getBaseThickness(),
            'base_price' => $this->getBasePrice(),
            'display_order' => $this->getDisplayOrder(),
            'is_active' => $this->isActive(),
            'is_default' => $this->isDefault(),
            'badge' => $this->getBadge(),
            'recommended_for' => $this->getRecommendedFor(),
            'available_options' => $this->attributes['available_options'] ?? [],
            'default_options' => $this->getDefaultOptions(),
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

    /**
     * Magic getter for property access
     */
    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }
}

<?php

namespace App\Models;

use Illuminate\Support\Collection;

/**
 * PaperType
 *
 * Central model for managing paper types throughout the application.
 * Provides a consistent interface for accessing paper specifications,
 * pricing, and display information.
 */
class PaperType
{
    protected array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get all active paper types
     *
     * @return Collection<PaperType>
     */
    public static function all(): Collection
    {
        $types = config('paper.types', []);

        return collect($types)
            ->filter(fn($type) => $type['is_active'] ?? true)
            ->sortBy('display_order')
            ->map(fn($type) => new self($type))
            ->values();
    }

    /**
     * Get all paper types including inactive
     *
     * @return Collection<PaperType>
     */
    public static function allWithInactive(): Collection
    {
        $types = config('paper.types', []);

        return collect($types)
            ->sortBy('display_order')
            ->map(fn($type) => new self($type))
            ->values();
    }

    /**
     * Find a paper type by ID
     *
     * @param string $id
     * @return PaperType|null
     */
    public static function find(string $id): ?self
    {
        $type = config("paper.types.{$id}");

        return $type ? new self($type) : null;
    }

    /**
     * Get the default paper type
     *
     * @return PaperType
     */
    public static function default(): self
    {
        $defaultId = config('paper.defaults.paper_type', 'standard');
        return self::find($defaultId) ?? self::find('standard');
    }

    /**
     * Find paper type by price (backward compatibility)
     *
     * @param float $price
     * @return PaperType|null
     */
    public static function findByPrice(float $price): ?self
    {
        $types = config('paper.types', []);

        foreach ($types as $type) {
            if (abs(($type['price_per_page'] ?? 0) - $price) < 0.001) {
                return new self($type);
            }
        }

        return null;
    }

    /**
     * Get paper types for a specific album type
     *
     * @param string $albumType
     * @return Collection<PaperType>
     */
    public static function forAlbum(string $albumType): Collection
    {
        $compatibility = config("paper.album_compatibility.{$albumType}", []);

        return self::all()->filter(fn($type) =>
            in_array($type->id, $compatibility)
        );
    }

    /**
     * Get ID
     */
    public function getId(): string
    {
        return $this->attributes['id'];
    }

    /**
     * Get name
     */
    public function getName(): string
    {
        return $this->attributes['name'];
    }

    /**
     * Get description
     */
    public function getDescription(): string
    {
        return $this->attributes['description'] ?? '';
    }

    /**
     * Get price per page
     */
    public function getPricePerPage(): float
    {
        return (float) ($this->attributes['price_per_page'] ?? 0);
    }

    /**
     * Get weight per page in ounces
     */
    public function getWeightPerPageOz(): float
    {
        return (float) ($this->attributes['weight_per_page_oz'] ?? 0);
    }

    /**
     * Get thickness in inches
     */
    public function getThicknessInches(): float
    {
        return (float) ($this->attributes['thickness_inches'] ?? 0);
    }

    /**
     * Get paper weight in lbs
     */
    public function getPaperWeightLbs(): int
    {
        return (int) ($this->attributes['paper_weight_lbs'] ?? 24);
    }

    /**
     * Get width in inches
     */
    public function getWidth(): float
    {
        return (float) ($this->attributes['width'] ?? 8.5);
    }

    /**
     * Get height in inches
     */
    public function getHeight(): float
    {
        return (float) ($this->attributes['height'] ?? 11.0);
    }

    /**
     * Get dimensions as string
     */
    public function getDimensionsString(): string
    {
        return $this->getWidth() . '" × ' . $this->getHeight() . '"';
    }

    /**
     * Get punch configuration
     */
    public function getPunches(): string
    {
        return $this->attributes['punches'] ?? '3-hole';
    }

    /**
     * Get color
     */
    public function getColor(): string
    {
        return $this->attributes['color'] ?? 'white';
    }

    /**
     * Get finish
     */
    public function getFinish(): string
    {
        return $this->attributes['finish'] ?? 'matte';
    }

    /**
     * Get opacity percentage
     */
    public function getOpacity(): int
    {
        return (int) ($this->attributes['opacity'] ?? 90);
    }

    /**
     * Get SKU prefix
     */
    public function getSkuPrefix(): string
    {
        return $this->attributes['sku_prefix'] ?? strtoupper(substr($this->getId(), 0, 3));
    }

    /**
     * Generate SKU for country/year
     */
    public function generateSku(string $country, ?string $year = null): string
    {
        $sku = $this->getSkuPrefix();
        $sku .= '-' . strtoupper(substr(preg_replace('/[^A-Z]/', '', strtoupper($country)), 0, 3));

        if ($year) {
            $sku .= '-' . $year;
        }

        return $sku;
    }

    /**
     * Get display badge
     */
    public function getBadge(): ?string
    {
        return $this->attributes['badge'] ?? null;
    }

    /**
     * Get recommended use case
     */
    public function getRecommendedFor(): ?string
    {
        return $this->attributes['recommended_for'] ?? null;
    }

    /**
     * Check if this is the default paper type
     */
    public function isDefault(): bool
    {
        return (bool) ($this->attributes['is_default'] ?? false);
    }

    /**
     * Check if active
     */
    public function isActive(): bool
    {
        return (bool) ($this->attributes['is_active'] ?? true);
    }

    /**
     * Get display order
     */
    public function getDisplayOrder(): int
    {
        return (int) ($this->attributes['display_order'] ?? 999);
    }

    /**
     * Calculate price for number of pages
     */
    public function calculatePrice(int $pages): float
    {
        return round($pages * $this->getPricePerPage(), 2);
    }

    /**
     * Calculate weight for number of pages (in ounces)
     */
    public function calculateWeight(int $pages): float
    {
        return round($pages * $this->getWeightPerPageOz(), 2);
    }

    /**
     * Calculate thickness for number of pages (in inches)
     */
    public function calculateThickness(int $pages): float
    {
        return round($pages * $this->getThicknessInches(), 3);
    }

    /**
     * Get all attributes
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Convert to JSON-friendly format for frontend
     */
    public function toJson(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'price_per_page' => $this->getPricePerPage(),
            'dimensions' => $this->getDimensionsString(),
            'punches' => $this->getPunches(),
            'color' => $this->getColor(),
            'finish' => $this->getFinish(),
            'badge' => $this->getBadge(),
            'recommended_for' => $this->getRecommendedFor(),
            'is_default' => $this->isDefault(),
        ];
    }

    /**
     * Get detailed specifications
     */
    public function getSpecifications(): array
    {
        return [
            'Physical' => [
                'Dimensions' => $this->getDimensionsString(),
                'Paper Weight' => $this->getPaperWeightLbs() . ' lb bond',
                'Weight per Page' => $this->getWeightPerPageOz() . ' oz',
                'Thickness' => $this->getThicknessInches() . '"',
                'Opacity' => $this->getOpacity() . '%',
            ],
            'Features' => [
                'Hole Punches' => $this->getPunches(),
                'Paper Color' => ucfirst($this->getColor()),
                'Surface Finish' => ucfirst($this->getFinish()),
            ],
            'Pricing' => [
                'Price per Page' => '$' . number_format($this->getPricePerPage(), 2),
            ],
        ];
    }

    /**
     * Magic getter for accessing attributes
     */
    public function __get(string $name)
    {
        $method = 'get' . str_replace('_', '', ucwords($name, '_'));

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return $this->attributes[$name] ?? null;
    }

    /**
     * Magic isset for checking attributes
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Convert to string (returns name)
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}

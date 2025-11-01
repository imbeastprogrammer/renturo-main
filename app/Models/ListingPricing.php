<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingPricing extends Model
{
    use HasFactory;

    protected $table = 'listing_pricing';

    protected $fillable = [
        'listing_id',
        'currency',
        'price_min',
        'price_max',
        'pricing_model',
        'base_hourly_price',
        'base_daily_price',
        'base_weekly_price',
        'base_monthly_price',
        'service_fee_percentage',
        'platform_fee_percentage',
        'security_deposit',
        'cleaning_fee',
        'weekly_discount_percentage',
        'monthly_discount_percentage',
        'min_price',
        'max_price',
        'price_per_guest',
        'tax_percentage',
        'tax_included',
        'effective_from',
        'effective_until',
    ];

    protected $casts = [
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'base_hourly_price' => 'decimal:2',
        'base_daily_price' => 'decimal:2',
        'base_weekly_price' => 'decimal:2',
        'base_monthly_price' => 'decimal:2',
        'service_fee_percentage' => 'decimal:2',
        'platform_fee_percentage' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'weekly_discount_percentage' => 'decimal:2',
        'monthly_discount_percentage' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'price_per_guest' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_included' => 'boolean',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Calculate total price with fees and taxes
     */
    public function calculateTotalPrice(
        float $basePrice,
        int $duration = 1,
        string $durationType = 'hourly',
        int $guests = 1,
        bool $applyDiscounts = true
    ): array {
        $subtotal = $basePrice * $duration;

        // Apply discounts
        $discount = 0;
        if ($applyDiscounts) {
            if ($durationType === 'daily' && $duration >= 7 && $this->weekly_discount_percentage) {
                $discount = ($subtotal * $this->weekly_discount_percentage) / 100;
            } elseif ($durationType === 'daily' && $duration >= 30 && $this->monthly_discount_percentage) {
                $discount = ($subtotal * $this->monthly_discount_percentage) / 100;
            }
        }

        $afterDiscount = $subtotal - $discount;

        // Extra guest fees
        $extraGuestFee = 0;
        if ($guests > 1 && $this->price_per_guest) {
            $extraGuestFee = ($guests - 1) * $this->price_per_guest;
        }

        // Service & platform fees
        $serviceFee = ($afterDiscount * $this->service_fee_percentage) / 100;
        $platformFee = ($afterDiscount * $this->platform_fee_percentage) / 100;

        // Cleaning & security deposit
        $cleaningFee = $this->cleaning_fee ?? 0;
        $securityDeposit = $this->security_deposit ?? 0;

        // Calculate taxes
        $taxableAmount = $afterDiscount + $serviceFee + $platformFee + $extraGuestFee + $cleaningFee;
        $taxes = $this->tax_included ? 0 : ($taxableAmount * $this->tax_percentage) / 100;

        // Total
        $total = $taxableAmount + $taxes + $securityDeposit;

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'after_discount' => round($afterDiscount, 2),
            'extra_guest_fee' => round($extraGuestFee, 2),
            'service_fee' => round($serviceFee, 2),
            'platform_fee' => round($platformFee, 2),
            'cleaning_fee' => round($cleaningFee, 2),
            'security_deposit' => round($securityDeposit, 2),
            'taxes' => round($taxes, 2),
            'total' => round($total, 2),
            'currency' => $this->currency,
        ];
    }

    /**
     * Get price for specific duration type
     */
    public function getPriceForDurationType(string $durationType): ?float
    {
        return match ($durationType) {
            'hourly' => $this->base_hourly_price,
            'daily' => $this->base_daily_price,
            'weekly' => $this->base_weekly_price,
            'monthly' => $this->base_monthly_price,
            default => null,
        };
    }

    /**
     * Update price range from units
     */
    public function updatePriceRangeFromUnits(): void
    {
        $listing = $this->listing;

        if ($listing->inventory_type === 'single') {
            // For single unit, use base prices
            $prices = array_filter([
                $this->base_hourly_price,
                $this->base_daily_price,
                $this->base_weekly_price,
                $this->base_monthly_price,
            ]);

            if (!empty($prices)) {
                $this->price_min = min($prices);
                $this->price_max = max($prices);
                $this->save();
            }
        } else {
            // For multiple units, get prices from dynamic_form_submissions
            $units = $listing->units;

            if ($units->isEmpty()) {
                return;
            }

            $allPrices = [];

            foreach ($units as $unit) {
                $unitData = $unit->data ?? [];
                
                // Extract all possible price fields from JSON
                $priceFields = [
                    'price_per_hour',
                    'price_per_day',
                    'price_per_week',
                    'price_per_month',
                    'base_hourly_price',
                    'base_daily_price',
                    'base_weekly_price',
                    'base_monthly_price',
                    'hourly_price',
                    'daily_price',
                    'weekly_price',
                    'monthly_price',
                ];

                foreach ($priceFields as $field) {
                    if (isset($unitData[$field]) && is_numeric($unitData[$field])) {
                        $allPrices[] = (float) $unitData[$field];
                    }
                }
            }

            if (!empty($allPrices)) {
                $this->price_min = min($allPrices);
                $this->price_max = max($allPrices);
                $this->save();
            }
        }
    }

    /**
     * Check if pricing is currently effective
     */
    public function isEffective(): bool
    {
        $now = now();

        if ($this->effective_from && $now->lt($this->effective_from)) {
            return false;
        }

        if ($this->effective_until && $now->gt($this->effective_until)) {
            return false;
        }

        return true;
    }

    /**
     * Format price with currency symbol
     */
    public function formatPrice(float $amount): string
    {
        $symbols = [
            'PHP' => '₱',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency . ' ';

        return $symbol . number_format($amount, 2);
    }

    /**
     * Get formatted price range
     */
    public function getFormattedPriceRangeAttribute(): string
    {
        if (!$this->price_min || !$this->price_max) {
            return 'Price not set';
        }

        if ($this->price_min == $this->price_max) {
            return $this->formatPrice($this->price_min);
        }

        return $this->formatPrice($this->price_min) . ' - ' . $this->formatPrice($this->price_max);
    }

    /**
     * Scopes
     */
    public function scopeEffective($query)
    {
        $now = now();
        
        return $query->where(function ($q) use ($now) {
            $q->whereNull('effective_from')
              ->orWhere('effective_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('effective_until')
              ->orWhere('effective_until', '>=', $now);
        });
    }

    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    public function scopePriceRange($query, ?float $minPrice = null, ?float $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('price_min', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('price_max', '<=', $maxPrice);
        }

        return $query;
    }
}


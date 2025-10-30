<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number',
        'booking_type',
        'listing_id',
        'listing_unit_id',
        'user_id',
        'owner_id',
        'booking_date',
        'check_in_date',
        'check_out_date',
        'check_in_time',
        'check_out_time',
        'duration_hours',
        'duration_days',
        'duration_type',
        'base_price',
        'subtotal',
        'service_fee',
        'cleaning_fee',
        'security_deposit',
        'tax_amount',
        'discount_amount',
        'total_price',
        'currency',
        'number_of_guests',
        'number_of_players',
        'number_of_vehicles',
        'guest_details',
        'status',
        'payment_status',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'cancellation_fee',
        'refund_amount',
        'special_requests',
        'owner_notes',
        'internal_notes',
        'confirmation_code',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'review_submitted',
        'review_submitted_at',
        'auto_confirmed',
        'requires_approval',
        'last_message_at',
        'unread_messages_count',
        'booking_metadata',
        'payment_method',
        'payment_transaction_id',
        'payment_completed_at',
        'booking_source',
        'platform',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'guest_details' => 'array',
        'booking_metadata' => 'array',
        'base_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'cancellation_fee' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'review_submitted' => 'boolean',
        'auto_confirmed' => 'boolean',
        'requires_approval' => 'boolean',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'review_submitted_at' => 'datetime',
        'last_message_at' => 'datetime',
        'payment_completed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function listingUnit(): BelongsTo
    {
        return $this->belongsTo(ListingUnit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'paid', 'checked_in', 'in_progress']);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('check_in_date', '>', now())
            ->whereIn('status', ['confirmed', 'paid']);
    }

    public function scopeCurrent($query)
    {
        return $query->where('check_in_date', '<=', now())
            ->where('check_out_date', '>=', now())
            ->whereIn('status', ['checked_in', 'in_progress']);
    }

    public function scopePast($query)
    {
        return $query->where('check_out_date', '<', now())
            ->whereIn('status', ['completed']);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeForListing($query, $listingId)
    {
        return $query->where('listing_id', $listingId);
    }

    /**
     * Business Logic Methods
     */
    
    /**
     * Generate unique booking number
     */
    public static function generateBookingNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastBooking = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastBooking ? intval(substr($lastBooking->booking_number, -6)) + 1 : 1;
        
        return sprintf('BK-%s%s-%06d', $year, $month, $sequence);
    }

    /**
     * Calculate total price with all fees
     */
    public function calculateTotalPrice(): float
    {
        $total = $this->subtotal 
            + $this->service_fee 
            + $this->cleaning_fee 
            + $this->tax_amount 
            - $this->discount_amount;
        
        return round($total, 2);
    }

    /**
     * Calculate duration in days
     */
    public function getDurationInDays(): int
    {
        return Carbon::parse($this->check_in_date)
            ->diffInDays(Carbon::parse($this->check_out_date));
    }

    /**
     * Calculate duration in hours
     */
    public function getDurationInHours(): int
    {
        if ($this->check_in_time && $this->check_out_time) {
            $checkIn = Carbon::parse($this->check_in_date . ' ' . $this->check_in_time);
            $checkOut = Carbon::parse($this->check_out_date . ' ' . $this->check_out_time);
            return $checkIn->diffInHours($checkOut);
        }
        
        return $this->duration_hours ?? 0;
    }

    /**
     * Check if booking is cancellable
     */
    public function isCancellable(): bool
    {
        if (in_array($this->status, ['cancelled', 'completed', 'expired', 'refunded'])) {
            return false;
        }

        // Check cancellation policy based on listing
        $hoursBeforeCheckIn = Carbon::now()->diffInHours(
            Carbon::parse($this->check_in_date . ' ' . ($this->check_in_time ?? '00:00:00')),
            false
        );

        $cancellationHours = $this->listing->cancellation_hours ?? 24;
        
        return $hoursBeforeCheckIn >= $cancellationHours;
    }

    /**
     * Check if booking is modifiable
     */
    public function isModifiable(): bool
    {
        if (in_array($this->status, ['cancelled', 'completed', 'expired', 'refunded'])) {
            return false;
        }

        return Carbon::now()->lessThan(Carbon::parse($this->check_in_date));
    }

    /**
     * Check if booking is active
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['confirmed', 'paid', 'checked_in', 'in_progress']);
    }

    /**
     * Check if booking has conflicts with existing bookings
     */
    public static function hasConflict($listingId, $checkInDate, $checkOutDate, $checkInTime = null, $checkOutTime = null, $excludeBookingId = null): bool
    {
        $query = self::where('listing_id', $listingId)
            ->whereIn('status', ['pending', 'confirmed', 'paid', 'checked_in', 'in_progress'])
            ->where(function ($q) use ($checkInDate, $checkOutDate, $checkInTime, $checkOutTime) {
                if ($checkInTime && $checkOutTime) {
                    // Hourly booking conflict check
                    $q->where(function ($subQ) use ($checkInDate, $checkOutDate, $checkInTime, $checkOutTime) {
                        $subQ->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                            ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                            ->orWhere(function ($dateQ) use ($checkInDate, $checkOutDate) {
                                $dateQ->where('check_in_date', '<=', $checkInDate)
                                    ->where('check_out_date', '>=', $checkOutDate);
                            });
                    });
                } else {
                    // Daily booking conflict check
                    $q->where(function ($subQ) use ($checkInDate, $checkOutDate) {
                        $subQ->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                            ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                            ->orWhere(function ($dateQ) use ($checkInDate, $checkOutDate) {
                                $dateQ->where('check_in_date', '<=', $checkInDate)
                                    ->where('check_out_date', '>=', $checkOutDate);
                            });
                    });
                }
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }

    /**
     * Confirm booking
     */
    public function confirm(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'confirmed';
        $this->confirmed_at = now();
        $this->confirmation_code = strtoupper(substr(md5(uniqid()), 0, 8));
        
        return $this->save();
    }

    /**
     * Mark as paid
     */
    public function markAsPaid($transactionId = null): bool
    {
        $this->payment_status = 'paid';
        $this->status = 'paid';
        $this->payment_completed_at = now();
        
        if ($transactionId) {
            $this->payment_transaction_id = $transactionId;
        }
        
        return $this->save();
    }

    /**
     * Check in
     */
    public function checkIn(): bool
    {
        if (!in_array($this->status, ['confirmed', 'paid'])) {
            return false;
        }

        $this->status = 'checked_in';
        $this->checked_in_at = now();
        
        return $this->save();
    }

    /**
     * Check out
     */
    public function checkOut(): bool
    {
        if ($this->status !== 'checked_in' && $this->status !== 'in_progress') {
            return false;
        }

        $this->status = 'completed';
        $this->checked_out_at = now();
        
        return $this->save();
    }

    /**
     * Cancel booking
     */
    public function cancel($cancelledBy, $reason = null): bool
    {
        if (!$this->isCancellable()) {
            return false;
        }

        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->cancelled_by = $cancelledBy;
        $this->cancellation_reason = $reason;
        
        // Calculate cancellation fee based on policy
        $this->cancellation_fee = $this->calculateCancellationFee();
        $this->refund_amount = $this->total_price - $this->cancellation_fee;
        
        return $this->save();
    }

    /**
     * Calculate cancellation fee
     */
    protected function calculateCancellationFee(): float
    {
        $hoursBeforeCheckIn = Carbon::now()->diffInHours(
            Carbon::parse($this->check_in_date . ' ' . ($this->check_in_time ?? '00:00:00')),
            false
        );

        // Example cancellation policy
        if ($hoursBeforeCheckIn >= 168) { // 7 days
            return 0; // Free cancellation
        } elseif ($hoursBeforeCheckIn >= 48) { // 2 days
            return $this->total_price * 0.25; // 25% fee
        } elseif ($hoursBeforeCheckIn >= 24) { // 1 day
            return $this->total_price * 0.50; // 50% fee
        } else {
            return $this->total_price; // No refund
        }
    }

    /**
     * Reject booking (owner action)
     */
    public function reject($reason = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'rejected';
        $this->cancellation_reason = $reason;
        
        return $this->save();
    }

    /**
     * Get status color for UI
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed', 'paid' => 'success',
            'checked_in', 'in_progress' => 'info',
            'completed' => 'primary',
            'cancelled', 'rejected' => 'danger',
            'expired' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }
}


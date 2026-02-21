<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quantity_available',
        'quantity_sold',
        'max_per_order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'quantity_available' => 'integer',
        'quantity_sold' => 'integer',
        'max_per_order' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'available_quantity',
        'is_sold_out',
        'formatted_price',
        'max_purchase_quantity',
        'sales_percentage'
    ];

    /**
     * Get the event that owns the ticket.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the registrations for this ticket.
     */
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'ticket_id');
    }

    /**
     * Get confirmed registrations for this ticket.
     */
    public function confirmedRegistrations()
    {
        return $this->registrations()->where('status', 'confirmed');
    }

    /**
     * Check if ticket is available for purchase.
     */
    public function isAvailable(int $quantity = 1): bool
    {
        return $this->available_quantity >= $quantity;
    }

    /**
     * Check if ticket has any available spots.
     */
    public function hasAvailability(): bool
    {
        return $this->available_quantity > 0;
    }

    /**
     * Check if ticket is sold out.
     */
    public function isSoldOut(): bool
    {
        return $this->available_quantity <= 0;
    }

    /**
     * Get available quantity attribute.
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity_available - $this->quantity_sold);
    }

    /**
     * Get is sold out attribute.
     */
    public function getIsSoldOutAttribute(): bool
    {
        return $this->available_quantity <= 0;
    }

    /**
     * Get formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get sales percentage attribute.
     */
    public function getSalesPercentageAttribute(): float
    {
        if ($this->quantity_available === 0) {
            return 0;
        }
        
        return round(($this->quantity_sold / $this->quantity_available) * 100, 2);
    }

    /**
     * Increment quantity sold.
     */
    public function incrementSold(int $quantity = 1): bool
    {
        if (!$this->isAvailable($quantity)) {
            return false;
        }

        $this->increment('quantity_sold', $quantity);
        
        // Refresh the model to update computed attributes
        $this->refresh();
        
        return true;
    }

    /**
     * Decrement quantity sold (for cancellations).
     */
    public function decrementSold(int $quantity = 1): bool
    {
        if ($this->quantity_sold < $quantity) {
            return false;
        }

        $this->decrement('quantity_sold', $quantity);
        
        // Refresh the model to update computed attributes
        $this->refresh();
        
        return true;
    }

    /**
     * Validate if quantity is within max per order limit.
     */
    public function validateMaxPerOrder(int $quantity): bool
    {
        if ($this->max_per_order === null || $this->max_per_order === 0) {
            return true;
        }
        
        return $quantity <= $this->max_per_order;
    }

    /**
     * Get the max purchase quantity (min of available and max_per_order).
     */
    public function getMaxPurchaseQuantityAttribute(): int
    {
        $maxByAvailability = $this->available_quantity;
        
        if ($this->max_per_order && $this->max_per_order > 0) {
            return min($maxByAvailability, $this->max_per_order);
        }
        
        return $maxByAvailability;
    }

    /**
     * Scope a query to only include available tickets.
     */
    public function scopeAvailable($query)
    {
        return $query->whereColumn('quantity_available', '>', 'quantity_sold');
    }

    /**
     * Scope a query to only include sold out tickets.
     */
    public function scopeSoldOut($query)
    {
        return $query->whereColumn('quantity_available', '<=', 'quantity_sold');
    }

    /**
     * Scope a query to only include tickets by price range.
     */
    public function scopePriceBetween($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope a query to only include free tickets.
     */
    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    /**
     * Scope a query to only include paid tickets.
     */
    public function scopePaid($query)
    {
        return $query->where('price', '>', 0);
    }

    /**
     * Scope a query to order by price.
     */
    public function scopeOrderByPrice($query, $direction = 'asc')
    {
        return $query->orderBy('price', $direction);
    }

    /**
     * Scope a query to order by popularity (most sold).
     */
    public function scopeOrderByPopularity($query, $direction = 'desc')
    {
        return $query->orderBy('quantity_sold', $direction);
    }
}
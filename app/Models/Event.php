<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'organizer_id',
        'start_date',
        'end_date',
        'venue',
        'banner_image',
        'is_published',
        'capacity'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'available_spots',
        'confirmed_registrations_count',
        'total_tickets_sold',
        'total_revenue',
        'status'
    ];

    /**
     * The booting method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($event) {
            $event->slug = $event->slug ?? Str::slug($event->title);
        });

        // Update slug when title changes
        static::updating(function ($event) {
            if ($event->isDirty('title') && !$event->isDirty('slug')) {
                $event->slug = Str::slug($event->title);
            }
        });

        // Prevent deletion if there are confirmed registrations
        static::deleting(function ($event) {
            if ($event->registrations()->where('status', 'confirmed')->exists()) {
                throw new \Exception('Cannot delete event with confirmed registrations.');
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the organizer of the event.
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Get the tickets for the event.
     */
    public function tickets()
    {
        return $this->hasMany(EventTicket::class);
    }

    /**
     * Get the registrations for the event.
     */
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Get the categories for the event.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'event_category')
                    ->withTimestamps();
    }

    /**
     * Check if event has available spots.
     */
    public function hasAvailableSpots(): bool
    {
        return $this->available_spots > 0;
    }

    /**
     * Get available spots count.
     */
    public function getAvailableSpotsAttribute(): int
    {
        $totalTicketsSold = $this->registrations()
            ->where('status', 'confirmed')
            ->sum('quantity');
        
        return max(0, $this->capacity - $totalTicketsSold);
    }

    /**
     * Get event status.
     */
    public function getStatusAttribute(): string
    {
        if ($this->isPast()) {
            return 'past';
        } elseif ($this->isOngoing()) {
            return 'ongoing';
        } elseif ($this->isUpcoming()) {
            return 'upcoming';
        }
        
        return 'unknown';
    }

    /**
     * Check if event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    /**
     * Check if event is ongoing.
     */
    public function isOngoing(): bool
    {
        return now()->between($this->start_date, $this->end_date);
    }

    /**
     * Check if event is past.
     */
    public function isPast(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Get total confirmed registrations count.
     */
    public function getConfirmedRegistrationsCountAttribute(): int
    {
        return $this->registrations()
            ->where('status', 'confirmed')
            ->count();
    }

    /**
     * Get total tickets sold count.
     */
    public function getTotalTicketsSoldAttribute(): int
    {
        return $this->registrations()
            ->where('status', 'confirmed')
            ->sum('quantity');
    }

    /**
     * Get total revenue from confirmed registrations.
     */
    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->registrations()
            ->where('status', 'confirmed')
            ->sum('total_amount');
    }

    /**
     * Get formatted capacity attribute.
     */
    public function getFormattedCapacityAttribute(): string
    {
        return $this->capacity ? number_format($this->capacity) . ' spots' : 'Unlimited';
    }

    /**
     * Get the banner image URL.
     */
    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner_image 
            ? asset('storage/' . $this->banner_image)
            : null;
    }

    /**
     * Scope a query to only include published events.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    /**
     * Scope a query to only include ongoing events.
     */
    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    /**
     * Scope a query to only include past events.
     */
    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Scope a query to only include events with available spots.
     */
    public function scopeHasAvailableSpots($query)
    {
        return $query->whereHas('registrations', function ($q) {
            $q->selectRaw('event_id, SUM(quantity) as total_sold')
              ->where('status', 'confirmed')
              ->groupBy('event_id')
              ->havingRaw('capacity > COALESCE(total_sold, 0)');
        });
    }

    /**
     * Scope a query to only include events by organizer.
     */
    public function scopeByOrganizer($query, $userId)
    {
        return $query->where('organizer_id', $userId);
    }

    /**
     * Scope a query to only include events by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    /**
     * Scope a query to search events by title or description.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%")
              ->orWhere('venue', 'LIKE', "%{$term}%");
        });
    }
}
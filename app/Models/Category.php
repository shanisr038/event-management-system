<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * The booting method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($category) {
            $category->slug = $category->slug ?? Str::slug($category->name);
        });

        // Update slug when name changes
        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
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
     * Get the events for the category.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_category');
    }

    /**
     * Get published events for this category.
     */
    public function publishedEvents()
    {
        return $this->events()->where('is_published', true);
    }

    /**
     * Get upcoming events for this category.
     */
    public function upcomingEvents()
    {
        return $this->events()
            ->where('is_published', true)
            ->where('start_date', '>', now());
    }

    /**
     * Get the events count attribute.
     */
    public function getEventsCountAttribute(): int
    {
        return $this->events()->count();
    }

    /**
     * Get the published events count attribute.
     */
    public function getPublishedEventsCountAttribute(): int
    {
        return $this->events()->where('is_published', true)->count();
    }

    /**
     * Get the formatted name with color.
     */
    public function getFormattedNameAttribute(): string
    {
        if ($this->color) {
            return "<span style='color: {$this->color}'>{$this->name}</span>";
        }
        return $this->name;
    }

    /**
     * Get the display name with icon.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->icon) {
            return "<i class='{$this->icon}'></i> {$this->name}";
        }
        return $this->name;
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive categories.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include categories with events.
     */
    public function scopeHasEvents($query)
    {
        return $query->whereHas('events');
    }

    /**
     * Scope a query to only include categories with published events.
     */
    public function scopeHasPublishedEvents($query)
    {
        return $query->whereHas('events', function ($q) {
            $q->where('is_published', true);
        });
    }

    /**
     * Scope a query to order by events count.
     */
    public function scopeOrderByEventsCount($query, $direction = 'desc')
    {
        return $query->withCount('events')->orderBy('events_count', $direction);
    }

    /**
     * Get the first letter of the category name for grouping.
     */
    public function getFirstLetterAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }
}
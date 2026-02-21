<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventRegistration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_registrations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'ticket_id',
        'quantity',
        'total_amount',
        'status',
        'ticket_code',
        'checked_in',
        'checked_in_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'quantity' => 'integer',
        'checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending',
        'checked_in' => false,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'formatted_total',
        'qr_code_url',
    ];

    /**
     * The booting method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate unique ticket code when creating
        static::creating(function ($registration) {
            $registration->ticket_code = $registration->generateUniqueTicketCode();
        });

        // Update ticket sold count when registration is confirmed
        static::updated(function ($registration) {
            if ($registration->wasChanged('status') && $registration->status === 'confirmed') {
                $registration->updateTicketSoldCount();
            }
            
            if ($registration->wasChanged('status') && $registration->status === 'cancelled') {
                $registration->revertTicketSoldCount();
            }
        });
    }

    /**
     * Generate a unique ticket code.
     */
    protected function generateUniqueTicketCode(): string
    {
        do {
            $code = 'TKT-' . strtoupper(Str::random(8));
        } while (static::where('ticket_code', $code)->exists());

        return $code;
    }

    /**
     * Get the event that owns the registration.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user that owns the registration.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket that owns the registration.
     */
    public function ticket()
    {
        return $this->belongsTo(EventTicket::class, 'ticket_id');
    }

    /**
     * Mark registration as checked in.
     */
    public function checkIn(): bool
    {
        if ($this->checked_in) {
            return false;
        }

        $this->checked_in = true;
        $this->checked_in_at = now();
        return $this->save();
    }

    /**
     * Mark registration as cancelled.
     */
    public function cancel(): bool
    {
        if ($this->status === 'cancelled') {
            return false;
        }

        $this->status = 'cancelled';
        return $this->save();
    }

    /**
     * Confirm the registration.
     */
    public function confirm(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'confirmed';
        return $this->save();
    }

    /**
     * Update ticket sold count when registration is confirmed.
     */
    protected function updateTicketSoldCount(): void
    {
        if ($this->ticket) {
            $this->ticket->incrementSold($this->quantity);
        }
    }

    /**
     * Revert ticket sold count when registration is cancelled.
     */
    protected function revertTicketSoldCount(): void
    {
        if ($this->ticket) {
            $this->ticket->decrementSold($this->quantity);
        }
    }

    /**
     * Check if registration can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return $this->status === 'confirmed' && !$this->checked_in;
    }

    /**
     * Check if registration can be checked in.
     */
    public function canBeCheckedIn(): bool
    {
        return $this->status === 'confirmed' && !$this->checked_in;
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    /**
     * Get QR code URL (for generating QR codes).
     */
    public function getQrCodeUrlAttribute(): string
    {
        return route('qr.code', ['code' => $this->ticket_code]);
    }

    /**
     * Get the ticket code with prefix.
     */
    public function getDisplayTicketCodeAttribute(): string
    {
        return $this->ticket_code;
    }

    /**
     * Scope a query to only include confirmed registrations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include pending registrations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include cancelled registrations.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include checked in registrations.
     */
    public function scopeCheckedIn($query)
    {
        return $query->where('checked_in', true);
    }

    /**
     * Scope a query to only include registrations for a specific event.
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope a query to only include registrations for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include registrations with a specific ticket.
     */
    public function scopeForTicket($query, $ticketId)
    {
        return $query->where('ticket_id', $ticketId);
    }
}
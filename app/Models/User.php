<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the events organized by this user.
     */
    public function organizedEvents()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    /**
     * Get the registrations for this user.
     */
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is an organizer.
     */
    public function isOrganizer(): bool
    {
        return $this->hasRole('organizer');
    }

    /**
     * Check if user is an attendee.
     */
    public function isAttendee(): bool
    {
        return $this->hasRole('attendee');
    }

    /**
     * Get the user's full name or email as fallback.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? explode('@', $this->email)[0];
    }

    /**
     * Get the avatar URL (with default fallback).
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar 
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? $this->email);
    }
}
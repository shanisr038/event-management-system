<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EventRegistration;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegistrationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any registrations.
     */
    public function viewAny(User $user): bool
    {
        // Users can view their own registrations via controller filtering
        return true;
    }

    /**
     * Determine whether the user can view the registration.
     */
    public function view(User $user, EventRegistration $registration): bool
    {
        // User can view if:
        // 1. They are the registrant
        // 2. They are the event organizer
        // 3. They are an admin
        return $user->id === $registration->user_id || 
               $user->id === $registration->event->organizer_id ||
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create registrations.
     */
    public function create(User $user, Event $event): bool
    {
        // Check if user has already registered for this event
        $existingRegistration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
            
        if ($existingRegistration) {
            return false;
        }
        
        // Check if event has available spots
        if (!$event->hasAvailableSpots()) {
            return false;
        }
        
        // Everyone (including guests) can register, but we handle guests separately
        return true;
    }

    /**
     * Determine whether the user can update the registration.
     */
    public function update(User $user, EventRegistration $registration): bool
    {
        // Typically registrations cannot be updated, only cancelled
        return false;
    }

    /**
     * Determine whether the user can delete the registration.
     */
    public function delete(User $user, EventRegistration $registration): bool
    {
        // Only admins can delete registrations permanently
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can cancel the registration.
     */
    public function cancel(User $user, EventRegistration $registration): bool
    {
        // User can cancel if:
        // 1. They are the registrant
        // 2. Registration is confirmed
        // 3. Not checked in yet
        // 4. Event hasn't started yet
        return $user->id === $registration->user_id && 
               $registration->status === 'confirmed' &&
               !$registration->checked_in &&
               $registration->event->start_date->isFuture();
    }

    /**
     * Determine whether the user can check in attendees.
     */
    public function checkIn(User $user, EventRegistration $registration): bool
    {
        // Only the event organizer or admin can check in attendees
        return ($user->id === $registration->event->organizer_id || 
                $user->hasRole('admin')) &&
               $registration->status === 'confirmed' &&
               !$registration->checked_in;
    }

    /**
     * Determine whether the user can view registration reports.
     */
    public function viewReports(User $user, Event $event): bool
    {
        // Only the event organizer or admin can view registration reports
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can export registrations.
     */
    public function export(User $user, Event $event): bool
    {
        // Only the event organizer or admin can export registrations
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }
}
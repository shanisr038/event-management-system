<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        // Everyone can view events list
        return true;
    }

    /**
     * Determine whether the user can view the event.
     */
    public function view(User $user, Event $event): bool
    {
        // Everyone can view published events
        if ($event->is_published) {
            return true;
        }
        
        // Only organizer and admin can view unpublished events
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        // Only organizers and admins can create events
        return $user->hasRole('organizer') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        // Only the organizer or admin can update
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        // Only the organizer or admin can delete
        // Check if event has no confirmed registrations
        if ($event->registrations()->where('status', 'confirmed')->exists()) {
            return false;
        }
        
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage the event (tickets, registrations, etc.)
     */
    public function manage(User $user, Event $event): bool
    {
        // Only the organizer or admin can manage event details
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can publish/unpublish the event.
     */
    public function publish(User $user, Event $event): bool
    {
        // Only the organizer or admin can publish
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view event reports.
     */
    public function viewReports(User $user, Event $event): bool
    {
        // Only the organizer or admin can view reports
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }
}
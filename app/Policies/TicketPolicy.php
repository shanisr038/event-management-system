<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;
use App\Models\EventTicket;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tickets.
     */
    public function viewAny(User $user, Event $event): bool
    {
        // Anyone can view tickets for published events
        if ($event->is_published) {
            return true;
        }
        
        // Only organizer and admin can view tickets for unpublished events
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, EventTicket $ticket): bool
    {
        $event = $ticket->event;
        
        // Anyone can view tickets for published events
        if ($event->is_published) {
            return true;
        }
        
        // Only organizer and admin can view tickets for unpublished events
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user, Event $event): bool
    {
        // Only the event organizer or admin can create tickets
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the ticket.
     */
    public function update(User $user, EventTicket $ticket): bool
    {
        $event = $ticket->event;
        
        // Only the event organizer or admin can update tickets
        // Check if ticket has sales
        if ($ticket->quantity_sold > 0) {
            return false; // Can't modify tickets that have been sold
        }
        
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, EventTicket $ticket): bool
    {
        $event = $ticket->event;
        
        // Only the event organizer or admin can delete tickets
        // Check if ticket has sales
        if ($ticket->quantity_sold > 0) {
            return false; // Can't delete tickets that have been sold
        }
        
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage ticket inventory.
     */
    public function manageInventory(User $user, Event $event): bool
    {
        // Only the event organizer or admin can manage ticket inventory
        return $user->id === $event->organizer_id || $user->hasRole('admin');
    }
}
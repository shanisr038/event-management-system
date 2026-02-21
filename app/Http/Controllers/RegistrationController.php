<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTicket;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    /**
     * Constructor - middleware is now handled in routes file
     * Remove the __construct method completely or use this:
     */
    
    /**
     * Show the form for creating a new registration.
     */
    public function create(Event $event)
    {
        $event->load(['tickets' => function($query) {
            $query->available();
        }]);
        
        // Check if event has available tickets
        $hasAvailableTickets = $event->tickets->isNotEmpty();

        if (!$hasAvailableTickets) {
            return redirect()->route('events.show', $event)
                           ->with('error', 'Sorry, no tickets available for this event.');
        }

        // Check if event has capacity
        if (!$event->hasAvailableSpots()) {
            return redirect()->route('events.show', $event)
                           ->with('error', 'Sorry, this event is full.');
        }

        return view('registrations.create', compact('event'));
    }

    /**
     * Store a newly created registration in storage.
     */
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'ticket_id' => 'required|exists:event_tickets,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Use transaction to prevent race conditions
        return DB::transaction(function () use ($request, $event) {
            $ticket = EventTicket::where('id', $request->ticket_id)
                                 ->where('event_id', $event->id)
                                 ->lockForUpdate()
                                 ->firstOrFail();

            // Check if ticket belongs to event
            if ($ticket->event_id !== $event->id) {
                return back()->with('error', 'Invalid ticket selection.');
            }

            // Check quantity limits
            if (!$ticket->validateMaxPerOrder($request->quantity)) {
                return back()->with('error', 'Maximum ' . $ticket->max_per_order . ' tickets per order.');
            }

            // Check availability
            if (!$ticket->isAvailable($request->quantity)) {
                return back()->with('error', 'Not enough tickets available.');
            }

            // Check event capacity
            if (!$event->hasAvailableSpots()) {
                return back()->with('error', 'Sorry, this event is full.');
            }

            // Calculate total
            $totalAmount = $ticket->price * $request->quantity;

            // Create registration
            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => Auth::id(), // Will be null for guests if you want to allow guest registrations
                'ticket_id' => $ticket->id,
                'quantity' => $request->quantity,
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'ticket_code' => $this->generateUniqueTicketCode(),
            ]);

            // Update ticket sold count using model method
            $ticket->incrementSold($request->quantity);

            return redirect()->route('registrations.confirmation', $registration)
                             ->with('success', 'Registration successful!');
        });
    }

    /**
     * Display registration confirmation.
     */
    public function confirmation(EventRegistration $registration)
    {
        // Check if user owns this registration or is organizer/admin
        if (Auth::id() !== $registration->user_id && 
            Auth::id() !== $registration->event->organizer_id && 
            !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $registration->load(['event', 'ticket']);
        
        return view('registrations.confirmation', compact('registration'));
    }

    /**
     * Display user's registrations.
     */
    public function myRegistrations()
    {
        $registrations = EventRegistration::with(['event', 'ticket'])
                                         ->where('user_id', Auth::id())
                                         ->latest()
                                         ->paginate(10);
        
        return view('registrations.my-registrations', compact('registrations'));
    }

    /**
     * Cancel a registration.
     */
    public function cancel(EventRegistration $registration)
    {
        // Check if user owns this registration
        if (Auth::id() !== $registration->user_id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$registration->canBeCancelled()) {
            return back()->with('error', 'This registration cannot be cancelled.');
        }

        DB::transaction(function () use ($registration) {
            // Update ticket sold count
            $registration->ticket->decrementSold($registration->quantity);

            // Update registration status
            $registration->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Registration cancelled successfully.');
    }

    /**
     * Check-in an attendee (for organizers).
     */
    public function checkIn(Event $event, EventRegistration $registration)
    {
        // Check if user is the event organizer or admin
        if (Auth::id() !== $event->organizer_id && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Verify registration belongs to this event
        if ($registration->event_id !== $event->id) {
            abort(404, 'Registration not found for this event.');
        }

        if (!$registration->canBeCheckedIn()) {
            return back()->with('error', 'This registration cannot be checked in.');
        }

        $registration->checkIn();

        return back()->with('success', 'Attendee checked in successfully.');
    }

    /**
     * Display registrations for a specific event (organizer view).
     */
    public function index(Event $event)
    {
        // Check if user is the event organizer or admin
        if (Auth::id() !== $event->organizer_id && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $registrations = EventRegistration::with(['user', 'ticket'])
            ->where('event_id', $event->id)
            ->latest()
            ->paginate(20);

        return view('registrations.index', compact('event', 'registrations'));
    }

    /**
     * Generate a unique ticket code.
     */
    private function generateUniqueTicketCode()
    {
        do {
            $code = 'TKT-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (EventRegistration::where('ticket_code', $code)->exists());

        return $code;
    }
}
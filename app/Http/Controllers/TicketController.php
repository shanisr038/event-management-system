<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTicket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of tickets for an event.
     */
    public function index(Event $event)
    {
        $this->authorize('manage', $event);
        
        $tickets = $event->tickets()->latest()->get();
        return view('tickets.index', compact('event', 'tickets'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create(Event $event)
    {
        $this->authorize('manage', $event);
        
        return view('tickets.create', compact('event'));
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('manage', $event);

        $validated = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:1',
            'max_per_order' => 'required|integer|min:1',
        ]);

        $validated['event_id'] = $event->id;
        $validated['quantity_sold'] = 0;

        EventTicket::create($validated);

        return redirect()->route('events.tickets.index', $event)
                         ->with('success', 'Ticket type created successfully.');
    }

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit(Event $event, EventTicket $ticket)
    {
        $this->authorize('manage', $event);
        
        return view('tickets.edit', compact('event', 'ticket'));
    }

    /**
     * Update the specified ticket in storage.
     */
    public function update(Request $request, Event $event, EventTicket $ticket)
    {
        $this->authorize('manage', $event);

        $validated = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:' . $ticket->quantity_sold,
            'max_per_order' => 'required|integer|min:1',
        ]);

        $ticket->update($validated);

        return redirect()->route('events.tickets.index', $event)
                         ->with('success', 'Ticket type updated successfully.');
    }

    /**
     * Remove the specified ticket from storage.
     */
    public function destroy(Event $event, EventTicket $ticket)
    {
        $this->authorize('manage', $event);

        if ($ticket->quantity_sold > 0) {
            return back()->with('error', 'Cannot delete ticket with existing registrations.');
        }

        $ticket->delete();

        return redirect()->route('events.tickets.index', $event)
                         ->with('success', 'Ticket type deleted successfully.');
    }
}
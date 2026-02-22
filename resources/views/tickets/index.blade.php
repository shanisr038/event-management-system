@extends('layouts.app')

@section('title', 'Manage Tickets')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="bi bi-tickets"></i> Tickets for: {{ $event->title }}
        </h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('events.tickets.create', $event->slug) }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Ticket
        </a>
        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Event
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if($tickets->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-ticket fs-1 d-block mb-3"></i>
                <h4>No Tickets Created</h4>
                <p class="mb-3">This event doesn't have any ticket types yet.</p>
                <a href="{{ route('events.tickets.create', $event->slug) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Ticket
                </a>
            </div>
        @else
            <div class="row">
                @foreach($tickets as $ticket)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-{{ $ticket->isAvailable() ? 'success' : 'secondary' }} text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $ticket->name }}</h5>
                                    <span class="badge bg-light text-dark">
                                        {{ $ticket->isAvailable() ? 'Available' : 'Sold Out' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="text-primary mb-3">${{ number_format($ticket->price, 2) }}</h3>
                                
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <strong>Available:</strong> {{ $ticket->available_quantity }} / {{ $ticket->quantity_available }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <strong>Sold:</strong> {{ $ticket->quantity_sold }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <strong>Max per order:</strong> {{ $ticket->max_per_order }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <strong>Sales:</strong> {{ $ticket->sales_percentage }}%
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100">
                                    <a href="{{ route('events.tickets.edit', [$event->slug, $ticket->id]) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('events.tickets.destroy', [$event->slug, $ticket->id]) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this ticket?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                {{ $ticket->quantity_sold > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
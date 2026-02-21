@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        @if($event->banner_image)
            <img src="{{ Storage::url($event->banner_image) }}" 
                 class="img-fluid rounded-3 mb-4 shadow" 
                 alt="{{ $event->title }}"
                 style="max-height: 400px; width: 100%; object-fit: cover;">
        @else
            <div class="bg-light rounded-3 mb-4 d-flex align-items-center justify-content-center" 
                 style="height: 300px;">
                <i class="bi bi-image text-secondary fs-1"></i>
                <span class="text-secondary ms-2">No banner image</span>
            </div>
        @endif
        
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h1 class="display-5">{{ $event->title }}</h1>
            
            @if($event->is_published)
                <span class="badge bg-success fs-6">Published</span>
            @else
                <span class="badge bg-warning fs-6">Draft</span>
            @endif
        </div>
        
        <div class="mb-3">
            @foreach($event->categories as $category)
                <a href="{{ route('events.category', $category) }}" 
                   class="badge bg-primary text-decoration-none me-1 p-2">
                    <i class="bi bi-tag"></i> {{ $category->name }}
                </a>
            @endforeach
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle"></i> Event Details
                        </h5>
                        <p class="card-text">
                            <i class="bi bi-calendar-event text-primary"></i> 
                            <strong>Start:</strong> {{ $event->start_date->format('F d, Y - h:i A') }}<br>
                            
                            <i class="bi bi-calendar-check text-primary"></i> 
                            <strong>End:</strong> {{ $event->end_date->format('F d, Y - h:i A') }}<br>
                            
                            <i class="bi bi-geo-alt text-primary"></i> 
                            <strong>Venue:</strong> {{ $event->venue }}<br>
                            
                            <i class="bi bi-person-circle text-primary"></i> 
                            <strong>Organizer:</strong> 
                            <a href="#" class="text-decoration-none">{{ $event->organizer->name }}</a><br>
                            
                            <i class="bi bi-people text-primary"></i> 
                            <strong>Capacity:</strong> 
                            <span class="{{ $event->available_spots > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $event->available_spots }} / {{ $event->capacity }} spots available
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mb-5">
            <h5>
                <i class="bi bi-file-text"></i> About This Event
            </h5>
            <div class="p-3 bg-light rounded">
                {!! nl2br(e($event->description)) !!}
            </div>
        </div>
        
        @auth
            @if(auth()->id() === $event->organizer_id || auth()->user()->isAdmin())
                <div class="mb-4">
                    <h5>
                        <i class="bi bi-gear"></i> Management
                    </h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('events.edit', $event) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Event
                        </a>
                        <a href="{{ route('events.tickets.index', $event) }}" class="btn btn-info">
                            <i class="bi bi-ticket"></i> Manage Tickets
                        </a>
                        <a href="{{ route('events.registrations.index', $event) }}" class="btn btn-secondary">
                            <i class="bi bi-people"></i> Registrations
                        </a>
                        
                        <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @endauth
    </div>
    
    <div class="col-md-4">
        <div class="card shadow sticky-top" style="top: 20px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-ticket-perforated"></i> Tickets
                </h5>
            </div>
            
            <div class="card-body">
                @if($event->tickets->isNotEmpty())
                    @foreach($event->tickets as $ticket)
                        <div class="mb-3 p-3 border rounded {{ $ticket->isAvailable() ? 'border-success' : 'border-secondary' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $ticket->name }}</h6>
                                <span class="badge {{ $ticket->isAvailable() ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $ticket->isAvailable() ? 'Available' : 'Sold Out' }}
                                </span>
                            </div>
                            
                            <p class="mt-2 mb-1">
                                <strong>Price:</strong> ${{ number_format($ticket->price, 2) }}<br>
                                <strong>Available:</strong> {{ $ticket->available_quantity }} of {{ $ticket->quantity_available }}<br>
                                <strong>Max per order:</strong> {{ $ticket->max_per_order }}
                            </p>
                            
                            @if($ticket->isAvailable() && $userRegistration ?? false)
                                <div class="alert alert-info py-2 mt-2">
                                    <i class="bi bi-check-circle"></i> You're registered!
                                </div>
                            @endif
                        </div>
                    @endforeach
                    
                    @auth
                        @if($event->is_published && $event->start_date->isFuture() && $event->hasAvailableSpots())
                            <a href="{{ route('events.register', $event) }}" 
                               class="btn btn-success w-100 mt-3">
                                <i class="bi bi-check-circle"></i> Register Now
                            </a>
                        @elseif(!$event->is_published)
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle"></i> 
                                This event is not published yet.
                            </div>
                        @elseif(!$event->hasAvailableSpots())
                            <div class="alert alert-danger mt-3">
                                <i class="bi bi-x-circle"></i> 
                                Sorry, this event is sold out.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i> 
                            Please <a href="{{ route('login') }}">login</a> to register for this event.
                        </div>
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Login to Register
                        </a>
                    @endauth
                @else
                    <div class="alert alert-secondary">
                        <i class="bi bi-info-circle"></i> 
                        No tickets available yet.
                    </div>
                    
                    @auth
                        @if(auth()->id() === $event->organizer_id || auth()->user()->isAdmin())
                            <a href="{{ route('events.tickets.create', $event) }}" 
                               class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Create Tickets
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
            
            <div class="card-footer bg-light">
                <small class="text-muted">
                    <i class="bi bi-clock"></i> 
                    Event {{ $event->start_date->isPast() ? 'started' : 'starts' }} 
                    {{ $event->start_date->diffForHumans() }}
                </small>
            </div>
        </div>
        
        <!-- Share buttons -->
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="bi bi-share"></i> Share this event</h6>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-envelope"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
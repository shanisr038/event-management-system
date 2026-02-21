@extends('layouts.app')

@section('title', 'Upcoming Events')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h1>Upcoming Events</h1>
        
        @if(request()->has('category') && isset($category))
            <p class="lead">Showing events in category: <span class="badge bg-primary">{{ $category->name }}</span></p>
            <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-secondary">Clear Filter</a>
        @endif
    </div>
    
    @auth
        @can('create', App\Models\Event::class)
            <div class="col text-end">
                <a href="{{ route('events.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Create New Event
                </a>
            </div>
        @endcan
    @endauth
</div>

<div class="row">
    @forelse($events as $event)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm hover-shadow">
                @if($event->banner_image)
                    <img src="{{ Storage::url($event->banner_image) }}" 
                         class="card-img-top" 
                         alt="{{ $event->title }}"
                         style="height: 200px; object-fit: cover;"
                         loading="lazy">
                @else
                    <div class="card-img-top bg-light text-secondary d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <i class="bi bi-image fs-1"></i>
                        <span class="ms-2">No Image</span>
                    </div>
                @endif
                
                <div class="card-body">
                    <h5 class="card-title">{{ $event->title }}</h5>
                    
                    <p class="card-text">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-calendar-event"></i> 
                            {{ $event->start_date->format('M d, Y - h:i A') }}
                        </small>
                        
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-geo-alt"></i> {{ $event->venue }}
                        </small>
                        
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-people"></i> 
                            {{ $event->available_spots }} spots available
                        </small>
                    </p>
                    
                    <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                    
                    @if($event->categories->isNotEmpty())
                        <div class="mb-2">
                            @foreach($event->categories as $category)
                                <a href="{{ route('events.category', $category) }}" 
                                   class="badge bg-primary text-decoration-none">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-person"></i> 
                            {{ $event->organizer->name ?? 'Unknown' }}
                        </small>
                        
                        @if($event->tickets->isNotEmpty())
                            <span class="badge bg-success">
                                From ${{ number_format($event->tickets->min('price'), 2) }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top-0">
                    <a href="{{ route('events.show', $event->slug) }}" 
                       class="btn btn-primary w-100">
                        <i class="bi bi-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                <h4>No events found</h4>
                <p class="mb-0">Check back later for upcoming events!</p>
                
                @auth
                    @can('create', App\Models\Event::class)
                        <a href="{{ route('events.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle"></i> Create Your First Event
                        </a>
                    @endcan
                @endauth
            </div>
        </div>
    @endforelse
</div>

@if($events->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <nav aria-label="Page navigation">
                {{ $events->links() }}
            </nav>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    .hover-shadow {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .card-img-top {
        border-top-left-radius: calc(0.375rem - 1px);
        border-top-right-radius: calc(0.375rem - 1px);
    }
    
    .badge {
        transition: opacity 0.2s;
    }
    
    .badge:hover {
        opacity: 0.8;
    }
</style>
@endpush
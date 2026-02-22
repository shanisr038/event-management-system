@extends('layouts.app')

@section('title', 'Create Ticket')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-ticket"></i> Create Ticket for: {{ $event->title }}
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('events.tickets.store', $event->slug) }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Ticket Name</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="e.g., Early Bird, VIP, Regular"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="price" class="form-label fw-bold">Price ($)</label>
                        <input type="number" 
                               step="0.01" 
                               min="0" 
                               class="form-control @error('price') is-invalid @enderror" 
                               id="price" 
                               name="price" 
                               value="{{ old('price', 0) }}" 
                               required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Enter 0 for free tickets</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity_available" class="form-label fw-bold">Quantity Available</label>
                        <input type="number" 
                               min="1" 
                               class="form-control @error('quantity_available') is-invalid @enderror" 
                               id="quantity_available" 
                               name="quantity_available" 
                               value="{{ old('quantity_available', 100) }}" 
                               required>
                        @error('quantity_available')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="max_per_order" class="form-label fw-bold">Maximum Per Order</label>
                        <input type="number" 
                               min="1" 
                               class="form-control @error('max_per_order') is-invalid @enderror" 
                               id="max_per_order" 
                               name="max_per_order" 
                               value="{{ old('max_per_order', 5) }}" 
                               required>
                        @error('max_per_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maximum number of tickets a user can buy in one order</small>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('events.tickets.index', $event->slug) }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
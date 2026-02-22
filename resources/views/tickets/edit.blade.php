@extends('layouts.app')

@section('title', 'Edit Ticket')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Edit Ticket: {{ $ticket->name }}
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('events.tickets.update', [$event->slug, $ticket->id]) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Ticket Name</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $ticket->name) }}" 
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
                               value="{{ old('price', $ticket->price) }}" 
                               required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity_available" class="form-label fw-bold">Quantity Available</label>
                        <input type="number" 
                               min="{{ $ticket->quantity_sold }}" 
                               class="form-control @error('quantity_available') is-invalid @enderror" 
                               id="quantity_available" 
                               name="quantity_available" 
                               value="{{ old('quantity_available', $ticket->quantity_available) }}" 
                               required>
                        @error('quantity_available')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Cannot be less than {{ $ticket->quantity_sold }} (already sold)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="max_per_order" class="form-label fw-bold">Maximum Per Order</label>
                        <input type="number" 
                               min="1" 
                               class="form-control @error('max_per_order') is-invalid @enderror" 
                               id="max_per_order" 
                               name="max_per_order" 
                               value="{{ old('max_per_order', $ticket->max_per_order) }}" 
                               required>
                        @error('max_per_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Sold so far:</strong> {{ $ticket->quantity_sold }} tickets
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('events.tickets.index', $event->slug) }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
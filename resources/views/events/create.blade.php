@extends('layouts.app')

@section('title', 'Create Event')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-plus"></i> Create New Event
                </h5>
            </div>
            
            <div class="card-body">
                {{-- Debug info - remove after fixing --}}
                @if($categories->count() > 0)
                    <div class="alert alert-success">
                        <strong>Debug:</strong> {{ $categories->count() }} categories loaded successfully.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> No categories found. Please contact administrator.
                    </div>
                @endif

                <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Event Title</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="Enter event title"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="5" 
                                  placeholder="Describe your event..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label fw-bold">Start Date & Time</label>
                            <input type="datetime-local" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date') }}"
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Must be in the future</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label fw-bold">End Date & Time</label>
                            <input type="datetime-local" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date') }}"
                                   required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Must be after start date</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="venue" class="form-label fw-bold">Venue/Location</label>
                        <input type="text" 
                               class="form-control @error('venue') is-invalid @enderror" 
                               id="venue" 
                               name="venue" 
                               value="{{ old('venue') }}" 
                               placeholder="Enter venue address or online link"
                               required>
                        @error('venue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="capacity" class="form-label fw-bold">Capacity</label>
                        <input type="number" 
                               class="form-control @error('capacity') is-invalid @enderror" 
                               id="capacity" 
                               name="capacity" 
                               value="{{ old('capacity', 100) }}" 
                               min="1" 
                               required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maximum number of attendees</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categories" class="form-label fw-bold">Categories</label>
                        <select class="form-select @error('categories') is-invalid @enderror" 
                                id="categories" 
                                name="categories[]" 
                                multiple
                                size="5">
                            @forelse($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @empty
                                <option value="" disabled>No categories available</option>
                            @endforelse
                        </select>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Hold Ctrl/Cmd (Windows) or Cmd (Mac) to select multiple
                        </small>
                        @error('categories')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="banner_image" class="form-label fw-bold">Banner Image</label>
                        <input type="file" 
                               class="form-control @error('banner_image') is-invalid @enderror" 
                               id="banner_image" 
                               name="banner_image" 
                               accept="image/*">
                        @error('banner_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Max size: 2MB. Accepted formats: JPG, PNG, GIF
                        </small>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="is_published" 
                               name="is_published" 
                               value="1"
                               {{ old('is_published') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">
                            Publish immediately (event will be visible to public)
                        </label>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('events.index') }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-set end date minimum based on start date
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
    });
    
    // Add this for debugging - remove after fixing
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Create view loaded successfully');
        console.log('Categories count: {{ $categories->count() }}');
    });
</script>
@endpush
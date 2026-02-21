@extends('layouts.app')

@section('title', 'My Registrations')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="bi bi-ticket-perforated"></i> My Registrations
        </h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('events.index') }}" class="btn btn-primary">
            <i class="bi bi-calendar-event"></i> Browse Events
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if($registrations->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-ticket fs-1 d-block mb-3"></i>
                <h4>No Registrations Found</h4>
                <p class="mb-3">You haven't registered for any events yet.</p>
                <a href="{{ route('events.index') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-event"></i> Browse Events
                </a>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Event</th>
                                    <th>Ticket</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Registered On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registrations as $registration)
                                    <tr>
                                        <td>
                                            <a href="{{ route('events.show', $registration->event->slug) }}" 
                                               class="text-decoration-none fw-bold">
                                                {{ $registration->event->title }}
                                            </a>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 
                                                {{ $registration->event->start_date->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $registration->ticket->name }}</span>
                                            <br>
                                            <small class="text-muted">
                                                ${{ number_format($registration->ticket->price, 2) }} each
                                            </small>
                                        </td>
                                        <td class="text-center">{{ $registration->quantity }}</td>
                                        <td class="fw-bold">${{ number_format($registration->total_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'confirmed' => 'success',
                                                    'pending' => 'warning',
                                                    'cancelled' => 'danger',
                                                ];
                                                $color = $statusColors[$registration->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ ucfirst($registration->status) }}
                                            </span>
                                            @if($registration->checked_in)
                                                <span class="badge bg-info">
                                                    <i class="bi bi-check-circle"></i> Checked In
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $registration->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $registration->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('registrations.confirmation', $registration) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                @if($registration->status === 'confirmed' && !$registration->checked_in && $registration->event->start_date->isFuture())
                                                    <button type="button" 
                                                            class="btn btn-outline-danger" 
                                                            title="Cancel Registration"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#cancelModal{{ $registration->id }}">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Cancel Modal -->
                                            @if($registration->status === 'confirmed' && !$registration->checked_in)
                                                <div class="modal fade" id="cancelModal{{ $registration->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">
                                                                    <i class="bi bi-exclamation-triangle"></i> 
                                                                    Cancel Registration
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to cancel your registration for:</p>
                                                                <p class="fw-bold">{{ $registration->event->title }}</p>
                                                                <p><strong>Ticket:</strong> {{ $registration->ticket->name }} x {{ $registration->quantity }}</p>
                                                                <p><strong>Total:</strong> ${{ number_format($registration->total_amount, 2) }}</p>
                                                                <p class="text-danger small">
                                                                    <i class="bi bi-info-circle"></i> 
                                                                    This action cannot be undone.
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="bi bi-x"></i> Close
                                                                </button>
                                                                <form action="{{ route('registrations.cancel', $registration) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="bi bi-check-circle"></i> Confirm Cancel
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Summary Cards -->
@if($registrations->isNotEmpty())
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Registrations</h6>
                    <h3 class="mb-0">{{ $registrations->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Confirmed</h6>
                    <h3 class="mb-0">{{ $registrations->where('status', 'confirmed')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Pending</h6>
                    <h3 class="mb-0">{{ $registrations->where('status', 'pending')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Spent</h6>
                    <h3 class="mb-0">${{ number_format($registrations->sum('total_amount'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    .table > :not(caption) > * > * {
        vertical-align: middle;
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
    }
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush
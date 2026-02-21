@extends('layouts.app')

@section('title', 'Event Registrations')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="bi bi-people"></i> Registrations for {{ $event->title }}
        </h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary">
            <i class="bi bi-eye"></i> View Event
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Attendee</th>
                                <th>Ticket</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Ticket Code</th>
                                <th>Check-in</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registrations as $registration)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $registration->user->name ?? 'Guest' }}
                                        <br>
                                        <small class="text-muted">{{ $registration->user->email ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $registration->ticket->name }}</td>
                                    <td class="text-center">{{ $registration->quantity }}</td>
                                    <td>${{ number_format($registration->total_amount, 2) }}</td>
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
                                    </td>
                                    <td>
                                        <code>{{ $registration->ticket_code }}</code>
                                    </td>
                                    <td>
                                        @if($registration->status === 'confirmed')
                                            @if($registration->checked_in)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Checked In
                                                    <br>
                                                    <small>{{ $registration->checked_in_at->format('h:i A') }}</small>
                                                </span>
                                            @else
                                                <form action="{{ route('events.registrations.check-in', [$event->slug, $registration]) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                            onclick="return confirm('Check in this attendee?')">
                                                        <i class="bi bi-check-circle"></i> Check In
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $registration->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $registration->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="bi bi-people fs-1 d-block mb-3"></i>
                                        <h5>No registrations yet</h5>
                                        <p class="text-muted">When people register for this event, they'll appear here.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $registrations->links() }}
        </div>
    </div>
</div>
@endsection
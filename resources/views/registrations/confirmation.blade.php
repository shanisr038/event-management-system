@extends('layouts.app')

@section('title', 'Registration Confirmation')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="bi bi-check-circle"></i> Registration Confirmed!
                </h4>
            </div>
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                <h2 class="mt-3">Thank You for Registering!</h2>
                <p class="lead">Your registration has been confirmed successfully.</p>
                
                <div class="row mt-4">
                    <div class="col-md-6 offset-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Event Details</h5>
                                <p class="mb-1">
                                    <strong>{{ $registration->event->title }}</strong>
                                </p>
                                <p class="mb-1">
                                    <i class="bi bi-calendar"></i> 
                                    {{ $registration->event->start_date->format('F d, Y - h:i A') }}
                                </p>
                                <p class="mb-1">
                                    <i class="bi bi-geo-alt"></i> {{ $registration->event->venue }}
                                </p>
                                <hr>
                                <h5>Ticket Information</h5>
                                <p class="mb-1">
                                    <strong>{{ $registration->ticket->name }}</strong> x {{ $registration->quantity }}
                                </p>
                                <p class="mb-1">
                                    Total: <strong>${{ number_format($registration->total_amount, 2) }}</strong>
                                </p>
                                <hr>
                                <h5>Ticket Code</h5>
                                <div class="bg-white p-3 rounded border">
                                    <code class="h4">{{ $registration->ticket_code }}</code>
                                </div>
                                <p class="text-muted small mt-2">
                                    <i class="bi bi-info-circle"></i> 
                                    Please save this code - you'll need it for check-in.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('events.index') }}" class="btn btn-primary">
                        <i class="bi bi-calendar-event"></i> Browse More Events
                    </a>
                    <a href="{{ route('my-registrations') }}" class="btn btn-outline-primary">
                        <i class="bi bi-ticket"></i> View My Registrations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
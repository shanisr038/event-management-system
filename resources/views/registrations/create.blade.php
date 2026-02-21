@extends('layouts.app')

@section('title', 'Register for Event')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-ticket"></i> Register for {{ $event->title }}
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('events.register.store', $event->slug) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="ticket_id" class="form-label">Select Ticket Type</label>
                        <select class="form-select @error('ticket_id') is-invalid @enderror" 
                                id="ticket_id" 
                                name="ticket_id" 
                                required>
                            <option value="">Choose a ticket type...</option>
                            @foreach($event->tickets as $ticket)
                                <option value="{{ $ticket->id }}" 
                                        data-price="{{ $ticket->price }}"
                                        data-available="{{ $ticket->available_quantity }}"
                                        data-max="{{ $ticket->max_per_order }}"
                                        {{ old('ticket_id') == $ticket->id ? 'selected' : '' }}>
                                    {{ $ticket->name }} - ${{ number_format($ticket->price, 2) }} 
                                    ({{ $ticket->available_quantity }} available)
                                </option>
                            @endforeach
                        </select>
                        @error('ticket_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" 
                               class="form-control @error('quantity') is-invalid @enderror" 
                               id="quantity" 
                               name="quantity" 
                               value="{{ old('quantity', 1) }}" 
                               min="1"
                               required>
                        <small class="text-muted" id="quantityHelp"></small>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Order Summary</h5>
                                <p class="mb-1">Ticket Price: <span id="ticketPrice">$0.00</span></p>
                                <p class="mb-1">Quantity: <span id="summaryQuantity">1</span></p>
                                <hr>
                                <h5 class="mb-0">Total: <span id="totalAmount">$0.00</span></h5>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Event
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Complete Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ticketSelect = document.getElementById('ticket_id');
        const quantityInput = document.getElementById('quantity');
        const ticketPriceSpan = document.getElementById('ticketPrice');
        const summaryQuantitySpan = document.getElementById('summaryQuantity');
        const totalAmountSpan = document.getElementById('totalAmount');
        const quantityHelp = document.getElementById('quantityHelp');

        function updateSummary() {
            const selectedOption = ticketSelect.options[ticketSelect.selectedIndex];
            if (selectedOption.value) {
                const price = parseFloat(selectedOption.dataset.price);
                const available = parseInt(selectedOption.dataset.available);
                const maxPerOrder = parseInt(selectedOption.dataset.max);
                const quantity = parseInt(quantityInput.value) || 1;

                // Update max attribute
                quantityInput.max = Math.min(available, maxPerOrder);
                quantityHelp.textContent = `Max ${quantityInput.max} tickets per order`;

                // Update display
                ticketPriceSpan.textContent = '$' + price.toFixed(2);
                summaryQuantitySpan.textContent = quantity;
                totalAmountSpan.textContent = '$' + (price * quantity).toFixed(2);
            } else {
                ticketPriceSpan.textContent = '$0.00';
                summaryQuantitySpan.textContent = quantityInput.value;
                totalAmountSpan.textContent = '$0.00';
                quantityHelp.textContent = '';
            }
        }

        ticketSelect.addEventListener('change', updateSummary);
        quantityInput.addEventListener('input', updateSummary);

        // Initial update
        if (ticketSelect.value) {
            updateSummary();
        }
    });
</script>
@endpush
@endsection
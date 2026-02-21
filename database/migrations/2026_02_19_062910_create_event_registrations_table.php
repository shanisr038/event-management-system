<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignId('ticket_id')
                ->constrained('event_tickets')
                ->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])
                ->default('pending');
            $table->string('ticket_code')->unique();
            $table->boolean('checked_in')->default(false);
            $table->datetime('checked_in_at')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('event_id');
            $table->index('user_id');
            $table->index('ticket_id');
            $table->index('status');
            $table->index('ticket_code');
            $table->index('checked_in');
            $table->index(['event_id', 'status']); // Composite index for common queries
            $table->index(['user_id', 'status']); // For user's registration lists
            $table->index(['event_id', 'user_id']); // Unique constraint alternative
            
            // Optional: Add unique constraint to prevent duplicate registrations
            // $table->unique(['event_id', 'user_id', 'ticket_id', 'status'], 'unique_active_registration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
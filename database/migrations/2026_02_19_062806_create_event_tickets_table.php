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
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity_available');
            $table->integer('quantity_sold')->default(0);
            $table->integer('max_per_order')->default(5);
            $table->timestamps();
            
            // Add check constraint to ensure quantity_sold doesn't exceed quantity_available
            // Note: MySQL doesn't enforce check constraints, but we'll add a comment
            // $table->check('quantity_sold <= quantity_available');
            
            // Add indexes
            $table->index('event_id');
            $table->index(['event_id', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_tickets');
    }
};
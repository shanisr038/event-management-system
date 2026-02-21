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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->foreignId('organizer_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->string('venue');
            $table->string('banner_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('capacity')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('organizer_id');
            $table->index('start_date');
            $table->index('is_published');
            $table->index(['start_date', 'is_published']); // Composite index for common queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
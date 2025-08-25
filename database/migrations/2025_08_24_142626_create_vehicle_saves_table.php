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
        Schema::create('vehicle_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('list_name')->default('My Saved Vehicles'); // Allow custom wishlist names
            $table->text('notes')->nullable(); // User notes about why they saved it
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamps();
            
            // Ensure one save per user per vehicle per list
            $table->unique(['user_id', 'vehicle_id', 'list_name']);
            
            // Indexes for performance
            $table->index(['user_id', 'saved_at']);
            $table->index(['vehicle_id', 'saved_at']);
            $table->index(['user_id', 'list_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_saves');
    }
};

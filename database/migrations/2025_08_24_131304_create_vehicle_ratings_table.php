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
        Schema::create('vehicle_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->text('comment')->nullable();
            $table->boolean('would_recommend')->default(true);
            $table->json('rating_categories')->nullable(); // cleanliness, punctuality, condition, etc.
            $table->boolean('is_featured')->default(false); // for highlighting good reviews
            $table->timestamp('rated_at')->useCurrent();
            $table->timestamps();
            
            // Ensure one rating per booking
            $table->unique('booking_id');
            
            // Index for faster queries
            $table->index(['vehicle_id', 'rating']);
            $table->index(['user_id', 'rated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_ratings');
    }
};

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
        Schema::create('vehicle_availability_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('blocked_date');
            $table->enum('block_type', ['maintenance', 'personal_use', 'repairs', 'seasonal', 'other'])->default('other');
            $table->string('reason', 255)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_type', ['weekly', 'monthly', 'yearly'])->nullable();
            $table->json('recurring_pattern')->nullable(); // For complex patterns like "every Monday"
            $table->date('recurring_end_date')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['vehicle_id', 'blocked_date']);
            $table->index(['blocked_date']);
            $table->unique(['vehicle_id', 'blocked_date']); // Prevent duplicate blocks for same vehicle/date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_availability_blocks');
    }
};

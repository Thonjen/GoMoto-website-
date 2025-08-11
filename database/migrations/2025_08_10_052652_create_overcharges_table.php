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
        Schema::create('overcharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('overcharge_type_id')->constrained('overcharge_types');
            $table->decimal('amount', 10, 2);
            $table->text('details')->nullable(); // JSON or text details about the overcharge
            $table->integer('units')->nullable(); // Hours late or KM out of city
            $table->decimal('rate_applied', 8, 2)->nullable(); // Rate that was applied
            $table->boolean('is_paid')->default(false);
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overcharges');
    }
};

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
        Schema::table('bookings', function (Blueprint $table) {
            // Add index for faster conflict checking
            $table->index(['vehicle_id', 'pickup_datetime', 'status']);
            
            // Add end_datetime column back temporarily for overlap detection
            // We'll calculate this based on pickup_datetime + pricing tier duration
            $table->datetime('calculated_end_datetime')->nullable()->after('pickup_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id', 'pickup_datetime', 'status']);
            $table->dropColumn('calculated_end_datetime');
        });
    }
};

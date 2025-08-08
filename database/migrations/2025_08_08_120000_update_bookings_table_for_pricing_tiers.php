<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add pricing tier relationship
            $table->foreignId('pricing_tier_id')->nullable()->after('vehicle_id')->constrained('vehicle_pricing_tiers');
            
            // Change start_datetime to pickup_datetime for clarity
            $table->renameColumn('start_datetime', 'pickup_datetime');
            
            // Remove end_datetime since we're using pricing tiers for duration
            $table->dropColumn('end_datetime');
            
            // Update status enum values
            $table->string('status', 20)->default('pending')->change(); // pending/confirmed/completed/cancelled
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['pricing_tier_id']);
            $table->dropColumn('pricing_tier_id');
            $table->renameColumn('pickup_datetime', 'start_datetime');
            $table->dateTime('end_datetime')->after('start_datetime');
        });
    }
};

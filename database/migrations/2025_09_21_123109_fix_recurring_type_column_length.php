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
        Schema::table('vehicle_availability_blocks', function (Blueprint $table) {
            // Increase the length of recurring_type column to accommodate 'custom_days'
            $table->string('recurring_type', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_availability_blocks', function (Blueprint $table) {
            // Revert back to original length (adjust as needed)
            $table->string('recurring_type', 10)->nullable()->change();
        });
    }
};

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
            // Drop the old enum constraint and recreate with new values
            $table->dropColumn('recurring_type');
        });
        
        Schema::table('vehicle_availability_blocks', function (Blueprint $table) {
            // Add the updated recurring_type with custom_days support
            $table->enum('recurring_type', ['weekly', 'monthly', 'yearly', 'custom_days'])->nullable()->after('is_recurring');
            
            // Add recurring_days column if it doesn't exist
            if (!Schema::hasColumn('vehicle_availability_blocks', 'recurring_days')) {
                $table->json('recurring_days')->nullable()->after('recurring_type');
            }
            
            // Add time-based blocking fields if they don't exist
            if (!Schema::hasColumn('vehicle_availability_blocks', 'is_time_based')) {
                $table->boolean('is_time_based')->default(false)->after('recurring_end_date');
            }
            
            if (!Schema::hasColumn('vehicle_availability_blocks', 'affects_whole_day')) {
                $table->boolean('affects_whole_day')->default(true)->after('is_time_based');
            }
            
            if (!Schema::hasColumn('vehicle_availability_blocks', 'start_time')) {
                $table->time('start_time')->nullable()->after('affects_whole_day');
            }
            
            if (!Schema::hasColumn('vehicle_availability_blocks', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_availability_blocks', function (Blueprint $table) {
            // Remove the added columns
            $table->dropColumn(['recurring_days', 'is_time_based', 'affects_whole_day', 'start_time', 'end_time']);
            
            // Drop and recreate the old enum
            $table->dropColumn('recurring_type');
        });
        
        Schema::table('vehicle_availability_blocks', function (Blueprint $table) {
            $table->enum('recurring_type', ['weekly', 'monthly', 'yearly'])->nullable()->after('is_recurring');
        });
    }
};

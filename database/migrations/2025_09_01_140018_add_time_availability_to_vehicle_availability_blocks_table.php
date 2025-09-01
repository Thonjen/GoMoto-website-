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
            // Add time-based availability fields
            $table->boolean('is_time_based')->default(false)->after('blocked_date');
            $table->time('start_time')->nullable()->after('is_time_based');
            $table->time('end_time')->nullable()->after('start_time');
            $table->boolean('affects_whole_day')->default(true)->after('end_time');
            
            // Add fields for recurring patterns with specific days
            $table->json('recurring_days')->nullable()->after('recurring_pattern'); // For days of week: [1,5] = Monday, Friday
            
            // Update block types to include time-based options
            $table->dropColumn('block_type');
        });
        
        // Re-add block_type with updated enum values
        Schema::table('vehicle_availability_blocks', function (Blueprint $table) {
            $table->enum('block_type', [
                'maintenance', 
                'personal_use', 
                'repairs', 
                'seasonal', 
                'time_restriction',
                'other'
            ])->default('other')->after('affects_whole_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_availability_blocks', function (Blueprint $table) {
            $table->dropColumn([
                'is_time_based',
                'start_time',
                'end_time',
                'affects_whole_day',
                'recurring_days',
                'block_type'
            ]);
        });
        
        // Restore original block_type enum
        Schema::table('vehicle_availability_blocks', function (Blueprint $table) {
            $table->enum('block_type', ['maintenance', 'personal_use', 'repairs', 'seasonal', 'other'])->default('other');
        });
    }
};

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
            $table->timestamp('actual_return_time')->nullable()->after('pickup_datetime');
            $table->decimal('pickup_latitude', 10, 8)->nullable()->after('actual_return_time');
            $table->decimal('pickup_longitude', 11, 8)->nullable()->after('pickup_latitude');
            $table->decimal('return_latitude', 10, 8)->nullable()->after('pickup_longitude');
            $table->decimal('return_longitude', 11, 8)->nullable()->after('return_latitude');
            $table->string('pickup_location_name')->nullable()->after('return_longitude');
            $table->string('return_location_name')->nullable()->after('pickup_location_name');
            $table->boolean('has_overcharges')->default(false)->after('return_location_name');
            $table->decimal('total_overcharges', 10, 2)->default(0)->after('has_overcharges');
            $table->boolean('is_extended')->default(false)->after('total_overcharges');
            $table->timestamp('extended_until')->nullable()->after('is_extended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'actual_pickup_time',
                'actual_return_time',
                'pickup_latitude',
                'pickup_longitude',
                'return_latitude',
                'return_longitude',
                'pickup_location_name',
                'return_location_name',
                'has_overcharges',
                'total_overcharges',
                'is_extended',
                'extended_until'
            ]);
        });
    }
};

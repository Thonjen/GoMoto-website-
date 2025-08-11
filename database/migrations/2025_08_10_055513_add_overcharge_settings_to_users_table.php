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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('late_return_rate', 8, 2)->default(100.00)->after('accepts_gcash');
            $table->decimal('out_of_city_base', 8, 2)->default(500.00)->after('late_return_rate');
            $table->decimal('out_of_city_rate', 8, 2)->default(50.00)->after('out_of_city_base');
            $table->integer('grace_period_minutes')->default(30)->after('out_of_city_rate');
            $table->boolean('enable_overcharges')->default(true)->after('grace_period_minutes');
            $table->text('overcharge_instructions')->nullable()->after('enable_overcharges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'late_return_rate',
                'out_of_city_base', 
                'out_of_city_rate',
                'grace_period_minutes',
                'enable_overcharges',
                'overcharge_instructions'
            ]);
        });
    }
};

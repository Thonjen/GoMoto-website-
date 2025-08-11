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
        Schema::table('vehicles', function (Blueprint $table) {
$table->foreignId('make_id')->nullable()->constrained('vehicle_makes')->onDelete('set null');
            $table->foreignId('model_id')->nullable()->after('make_id')->constrained('vehicle_models')->onDelete('set null');
            $table->foreignId('transmission_id')->nullable()->after('fuel_type_id')->constrained('transmissions')->onDelete('set null');
            $table->string('engine_size')->nullable()->after('transmission_id');
            $table->integer('horsepower')->nullable()->after('engine_size');
            $table->integer('doors')->nullable()->after('horsepower');
            $table->integer('seats')->nullable()->after('doors');
            $table->decimal('fuel_efficiency', 4, 1)->nullable()->after('seats')->comment('km/l or mpg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['make_id']);
            $table->dropForeign(['model_id']);
            $table->dropForeign(['transmission_id']);
            $table->dropColumn([
                'make_id',
                'model_id', 
                'transmission_id',
                'engine_size',
                'horsepower',
                'doors',
                'seats',
                'fuel_efficiency'
            ]);
        });
    }
};

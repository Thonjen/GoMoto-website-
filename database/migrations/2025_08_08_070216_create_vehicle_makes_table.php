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
        Schema::create('vehicle_makes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('vehicle_type', 20); // 'car' or 'motorcycle'
            $table->integer('make_id')->nullable(); // NHTSA API ID
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['name', 'vehicle_type']); // Allow same make for different types
            $table->index(['vehicle_type', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_makes');
    }
};

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
        Schema::create('overcharge_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // late_return, out_of_city
            $table->string('description');
            $table->decimal('rate_per_hour', 8, 2)->nullable(); // For late return
            $table->decimal('rate_per_km', 8, 2)->nullable(); // For out of city
            $table->decimal('base_rate', 8, 2)->nullable(); // Base charge
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overcharge_types');
    }
};

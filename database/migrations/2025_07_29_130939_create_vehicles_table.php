<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('type_id')->constrained('vehicle_types');
            $table->foreignId('fuel_type_id')->constrained('fuel_types');
            $table->string('license_plate', 20)->unique();
            $table->integer('year');
            $table->string('color', 50);
            $table->boolean('is_available')->default(true);
            $table->text('description')->nullable();
            $table->string('main_photo_url')->nullable();
            $table->decimal('lat', 9, 6)->nullable();
            $table->decimal('lng', 9, 6)->nullable();
            $table->string('location_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};

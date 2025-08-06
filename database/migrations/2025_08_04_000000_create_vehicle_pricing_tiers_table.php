<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclePricingTiersTable extends Migration
{
    public function up()
    {
        Schema::create('vehicle_pricing_tiers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_id');
            $table->integer('duration_from');
            $table->enum('duration_unit', ['minutes', 'hours', 'days']);
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_pricing_tiers');
    }
}


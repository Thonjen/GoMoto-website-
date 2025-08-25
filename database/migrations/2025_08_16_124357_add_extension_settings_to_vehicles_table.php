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
            $table->boolean('allow_extensions')->default(true)->after('is_available');
            $table->integer('max_extension_hours')->default(72)->after('allow_extensions');
            $table->boolean('require_approval_for_extensions')->default(true)->after('max_extension_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'allow_extensions',
                'max_extension_hours', 
                'require_approval_for_extensions'
            ]);
        });
    }
};

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
            // JSON column to store owner's accepted payment methods
            $table->json('accepted_payment_methods')->nullable()->after('gcash_qr');
            // Whether owner accepts cash on delivery
            $table->boolean('accepts_cod')->default(true)->after('accepted_payment_methods');
            // Whether owner accepts GCash (only if QR is uploaded)
            $table->boolean('accepts_gcash')->default(false)->after('accepts_cod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['accepted_payment_methods', 'accepts_cod', 'accepts_gcash']);
        });
    }
};

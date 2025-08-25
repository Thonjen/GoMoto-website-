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
            // Bio field for owners
            $table->text('bio')->nullable();
            
            // Driver's license verification fields
            $table->string('drivers_license_front')->nullable();
            $table->string('drivers_license_back')->nullable();
            $table->timestamp('license_submitted_at')->nullable();
            
            // KYC verification status
            $table->enum('kyc_status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('kyc_rejection_reason')->nullable();
            $table->timestamp('kyc_verified_at')->nullable();
            $table->unsignedBigInteger('kyc_verified_by')->nullable();
            
            // Account restrictions
            $table->boolean('can_book')->default(false);
            $table->boolean('can_list_vehicles')->default(false);
            
            $table->foreign('kyc_verified_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kyc_verified_by']);
            $table->dropColumn([
                'bio',
                'drivers_license_front',
                'drivers_license_back',
                'license_submitted_at',
                'kyc_status',
                'kyc_rejection_reason',
                'kyc_verified_at',
                'kyc_verified_by',
                'can_book',
                'can_list_vehicles'
            ]);
        });
    }
};

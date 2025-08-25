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
        Schema::create('kyc_verification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User being verified
            $table->unsignedBigInteger('admin_id'); // Admin performing verification
            $table->enum('action', ['submitted', 'approved', 'rejected', 'resubmitted']);
            $table->enum('old_status', ['pending', 'under_review', 'approved', 'rejected'])->nullable();
            $table->enum('new_status', ['pending', 'under_review', 'approved', 'rejected']);
            $table->text('reason')->nullable(); // Reason for rejection or notes
            $table->json('documents_checked')->nullable(); // Which documents were reviewed
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users');
            
            $table->index(['user_id', 'created_at']);
            $table->index(['admin_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_verification_logs');
    }
};

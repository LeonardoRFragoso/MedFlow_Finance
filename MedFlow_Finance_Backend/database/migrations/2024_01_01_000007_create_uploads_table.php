<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clinic_id');
            $table->uuid('user_id')->nullable();
            
            $table->string('original_filename');
            $table->string('file_path');
            $table->bigInteger('file_size_bytes');
            $table->enum('file_type', ['excel', 'csv', 'xml']);
            $table->string('file_hash')->nullable();
            
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('processing_completed_at')->nullable();
            $table->text('processing_error_message')->nullable();
            
            $table->integer('total_rows')->default(0);
            $table->integer('valid_rows')->default(0);
            $table->integer('error_rows')->default(0);
            $table->integer('warning_rows')->default(0);
            
            $table->date('billing_period_start')->nullable();
            $table->date('billing_period_end')->nullable();
            
            $table->text('description')->nullable();
            $table->json('tags')->default('[]');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index('clinic_id');
            $table->index('status');
            $table->index(['clinic_id', 'status']);
            $table->index(['clinic_id', 'created_at']);
            $table->index(['clinic_id', 'file_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};

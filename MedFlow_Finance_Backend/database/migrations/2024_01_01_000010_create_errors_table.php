<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('errors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clinic_id');
            $table->uuid('upload_id');
            $table->uuid('record_id')->nullable();
            
            $table->enum('error_type', ['parse', 'validation', 'processing', 'system']);
            $table->string('error_code')->nullable();
            $table->text('error_message');
            
            $table->integer('row_number')->nullable();
            $table->string('field_name')->nullable();
            $table->text('raw_value')->nullable();
            
            $table->text('stack_trace')->nullable();
            
            $table->enum('status', ['new', 'acknowledged', 'resolved'])->default('new');
            $table->timestamp('resolved_at')->nullable();
            $table->uuid('resolved_by')->nullable();
            $table->text('resolution_notes')->nullable();
            
            $table->timestamps();
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('upload_id')->references('id')->on('uploads')->onDelete('cascade');
            $table->foreign('record_id')->references('id')->on('records')->onDelete('set null');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('clinic_id');
            $table->index('upload_id');
            $table->index(['clinic_id', 'status']);
            $table->index(['clinic_id', 'error_type']);
            $table->index(['clinic_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('errors');
    }
};

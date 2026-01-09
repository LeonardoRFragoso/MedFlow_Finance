<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clinic_id');
            $table->uuid('record_id');
            $table->uuid('upload_id');
            
            $table->string('rule_name');
            $table->enum('rule_type', ['field', 'business', 'compliance', 'glosa']);
            
            $table->boolean('is_valid');
            $table->enum('severity', ['error', 'warning', 'info'])->default('error');
            
            $table->string('field_name')->nullable();
            $table->text('expected_value')->nullable();
            $table->text('actual_value')->nullable();
            $table->text('message');
            
            $table->json('rule_config')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('record_id')->references('id')->on('records')->onDelete('cascade');
            $table->foreign('upload_id')->references('id')->on('uploads')->onDelete('cascade');
            
            $table->index('clinic_id');
            $table->index('record_id');
            $table->index('upload_id');
            $table->index(['clinic_id', 'is_valid']);
            $table->index(['clinic_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};

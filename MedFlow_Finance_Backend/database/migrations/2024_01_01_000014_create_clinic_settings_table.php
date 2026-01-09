<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clinic_id')->unique();
            
            $table->string('default_billing_type')->default('private');
            $table->string('currency', 3)->default('BRL');
            
            $table->boolean('enable_glosa_detection')->default(true);
            $table->boolean('enable_compliance_check')->default(true);
            $table->json('validation_rules')->default('{}');
            
            $table->integer('data_retention_days')->default(2555);
            $table->boolean('auto_delete_old_files')->default(false);
            
            $table->boolean('notify_on_upload_complete')->default(true);
            $table->boolean('notify_on_error')->default(true);
            $table->string('notification_email')->nullable();
            
            $table->string('webhook_url')->nullable();
            $table->string('webhook_secret')->nullable();
            
            $table->timestamps();
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->index('clinic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};

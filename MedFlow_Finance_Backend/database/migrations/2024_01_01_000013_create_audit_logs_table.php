<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clinic_id');
            $table->uuid('user_id')->nullable();
            
            $table->string('action');
            $table->string('resource_type');
            $table->uuid('resource_id')->nullable();
            
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('http_method')->nullable();
            $table->string('http_path')->nullable();
            $table->integer('http_status_code')->nullable();
            
            $table->enum('status', ['success', 'failure'])->default('success');
            $table->text('error_message')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index('clinic_id');
            $table->index('user_id');
            $table->index(['clinic_id', 'resource_type', 'resource_id']);
            $table->index(['clinic_id', 'action']);
            $table->index(['clinic_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

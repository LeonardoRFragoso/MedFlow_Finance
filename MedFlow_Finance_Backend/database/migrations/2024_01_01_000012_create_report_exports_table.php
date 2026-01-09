<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_exports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clinic_id');
            $table->uuid('report_id');
            $table->uuid('user_id')->nullable();
            
            $table->enum('export_format', ['csv', 'pdf', 'xlsx', 'json']);
            
            $table->string('file_path');
            $table->bigInteger('file_size_bytes')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('expires_at');
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index('clinic_id');
            $table->index('report_id');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_exports');
    }
};

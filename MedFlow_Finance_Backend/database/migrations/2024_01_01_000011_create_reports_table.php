<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clinic_id');
            $table->uuid('user_id')->nullable();
            
            $table->enum('report_type', ['summary', 'detailed', 'errors', 'validation', 'financial']);
            
            $table->date('period_start');
            $table->date('period_end');
            
            $table->integer('total_records')->default(0);
            $table->integer('total_valid')->default(0);
            $table->integer('total_errors')->default(0);
            $table->integer('total_warnings')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->json('content');
            
            $table->timestamp('generated_at');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index('clinic_id');
            $table->index(['clinic_id', 'report_type']);
            $table->index(['clinic_id', 'period_start', 'period_end']);
            $table->index(['clinic_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

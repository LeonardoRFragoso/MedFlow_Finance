<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clinic_id');
            $table->uuid('upload_id');
            
            $table->string('patient_name')->nullable();
            $table->string('patient_cpf')->nullable();
            $table->string('patient_id')->nullable();
            
            $table->string('procedure_code');
            $table->string('procedure_name')->nullable();
            $table->date('procedure_date');
            
            $table->decimal('amount_billed', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('amount_pending', 12, 2)->default(0);
            
            $table->enum('status', ['pending', 'approved', 'rejected', 'disputed'])->default('pending');
            
            $table->string('provider_name')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('insurance_name')->nullable();
            $table->string('insurance_id')->nullable();
            $table->string('authorization_number')->nullable();
            
            $table->json('raw_data');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('upload_id')->references('id')->on('uploads')->onDelete('cascade');
            
            $table->index('clinic_id');
            $table->index('upload_id');
            $table->index(['clinic_id', 'procedure_code']);
            $table->index(['clinic_id', 'patient_cpf']);
            $table->index(['clinic_id', 'procedure_date']);
            $table->index(['clinic_id', 'status']);
            $table->index(['clinic_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};

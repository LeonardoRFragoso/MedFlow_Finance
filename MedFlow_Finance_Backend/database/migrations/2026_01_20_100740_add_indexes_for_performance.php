<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            // Índice composto para filtros comuns
            $table->index(['clinic_id', 'status', 'created_at'], 'idx_uploads_clinic_status_created');
            $table->index(['clinic_id', 'billing_period_start', 'billing_period_end'], 'idx_uploads_clinic_billing_period');
        });

        Schema::table('records', function (Blueprint $table) {
            // Índice composto para queries frequentes
            $table->index(['clinic_id', 'upload_id', 'status'], 'idx_records_clinic_upload_status');
            $table->index(['clinic_id', 'procedure_date'], 'idx_records_clinic_procedure_date');
        });

        Schema::table('validations', function (Blueprint $table) {
            // Índice para busca de validações por registro
            $table->index(['record_id', 'severity'], 'idx_validations_record_severity');
        });
    }

    public function down(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropIndex('idx_uploads_clinic_status_created');
            $table->dropIndex('idx_uploads_clinic_billing_period');
        });

        Schema::table('records', function (Blueprint $table) {
            $table->dropIndex('idx_records_clinic_upload_status');
            $table->dropIndex('idx_records_clinic_procedure_date');
        });

        Schema::table('validations', function (Blueprint $table) {
            $table->dropIndex('idx_validations_record_severity');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('cnpj')->unique();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code')->nullable();
            
            $table->enum('billing_type', ['private', 'public', 'mixed'])->default('private');
            $table->string('default_currency', 3)->default('BRL');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            
            $table->string('plan_type')->default('basic');
            $table->timestamp('plan_started_at')->nullable();
            $table->timestamp('plan_expires_at')->nullable();
            
            $table->integer('max_users')->default(5);
            $table->integer('max_monthly_uploads')->default(100);
            $table->integer('max_file_size_mb')->default(100);
            
            $table->string('logo_url')->nullable();
            $table->string('custom_domain')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};

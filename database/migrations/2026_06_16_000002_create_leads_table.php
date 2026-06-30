<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->string('contact_name');
            $table->string('contact_mobile');
            $table->string('alternate_mobile')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            
            $table->enum('source', ['website', 'facebook', 'instagram', 'google', 'referral', 'builder', 'architect', 'walk_in', 'other'])->default('other');
            $table->enum('lead_type', ['residential', 'commercial'])->default('residential');
            $table->enum('project_type', ['villa', 'apartment', 'office', 'shop', 'hotel'])->nullable();
            $table->string('expected_budget')->nullable();
            $table->text('project_address')->nullable();
            
            $table->enum('status', [
                'new', 'contacted', 'site_visit_scheduled', 'measurement_pending', 
                'measurement_completed', 'quotation_sent', 'negotiation', 'confirmed', 'lost'
            ])->default('new');
            
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

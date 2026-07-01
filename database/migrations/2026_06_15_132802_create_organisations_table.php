<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('gst_number')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('theme_color')->default('#fc720d');
            
            // Branding & Settings
            $table->text('terms_and_conditions')->nullable();
            $table->text('quotation_footer')->nullable();
            $table->json('pdf_branding')->nullable();
            
            // Subscription
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans');
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->enum('active_status', ['active', 'suspended', 'expired'])->default('active');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};

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
        Schema::create('organisation_module_usage_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('organisation_subscriptions')->cascadeOnDelete();
            $table->timestamp('enabled_at')->useCurrent();
            $table->timestamp('disabled_at')->nullable();
            $table->boolean('is_chargeable')->default(false);
            $table->string('billing_cycle', 20)->index()->comment('e.g. 2026-07');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_module_usage_histories');
    }
};

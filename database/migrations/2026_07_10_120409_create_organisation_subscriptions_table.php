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
        Schema::create('organisation_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->datetime('grace_until')->nullable();
            $table->enum('status', ['Trial', 'Active', 'Grace', 'Cancelled', 'Suspended']);
            $table->enum('type', ['Trial', 'Monthly', 'Yearly', 'Lifetime']);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_subscriptions');
    }
};

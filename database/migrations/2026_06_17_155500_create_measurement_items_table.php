<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('measurement_id')->constrained('measurements')->onDelete('cascade');
            $table->string('title'); // e.g. Bedroom Window
            $table->text('description')->nullable(); // e.g. H=12, W=4
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurement_items');
    }
};

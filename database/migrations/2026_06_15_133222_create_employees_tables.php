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
        // 1. Employees (Additional Profile Info)
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->foreignId('user_id')->unique()->constrained('users');
            $table->string('employee_code')->unique();
            $table->string('designation')->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('salary', 15, 2)->default(0);
            $table->boolean('auto_generate_salary')->default(false)->after('salary');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

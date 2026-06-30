<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('gst_number')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->date('expense_date');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank', 'upi', 'card', 'cheque'])->default('cash');
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Seed default categories for existing organisations
        $organisations = DB::table('organisations')->get();
        $categories = [
            'Raw Material', 'Glass', 'Hardware', 'Transportation', 'Fuel',
            'Office Expense', 'Marketing', 'Salary', 'Rent', 'Electricity',
            'Internet', 'Maintenance', 'Miscellaneous', 'Other'
        ];

        foreach ($organisations as $org) {
            foreach ($categories as $cat) {
                DB::table('expense_categories')->insert([
                    'organisation_id' => $org->id,
                    'name' => $cat,
                    'slug' => Str::slug($cat),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('expense_categories');
    }
};

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
        // Add new Enum 'trial'.
        // MySQL requires raw statement for altering enum (or adding string column instead of enum for simpler updates).
        // A simpler approach for ENUM altering in Laravel without doctrine DBAL is usually raw DB statements, 
        // OR simply creating fields. Let's just create the string/enum fields.
        
        // Wait, Laravel schema builder cannot natively modify enum columns safely across SQLite/MySQL without dbal. Let's use a simple string for payment_status and contact_person.
        
        Schema::table('organisations', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->after('name');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('payment_status')->default('paid')->after('active_status');
        });
        
        // Since we are also updating the original schema, we should avoid touching `active_status` here if it's too complex. 
        // Instead, just use DB::statement for MySQL (the user's OS is linux and likely MySQL):
        DB::statement("ALTER TABLE organisations MODIFY COLUMN active_status ENUM('active', 'suspended', 'trial', 'inactive', 'expired') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'city', 'state', 'country', 'payment_status']);
        });
    }
};

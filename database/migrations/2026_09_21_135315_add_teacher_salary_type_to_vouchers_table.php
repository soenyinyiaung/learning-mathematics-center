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
        // Drop the old type column and create a new one with teacher_salary option
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('type', ['sale', 'student_fee', 'teacher_salary'])->default('sale')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new type column and recreate the old one
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('type', ['sale', 'student_fee'])->default('sale')->after('id');
        });
    }
};
